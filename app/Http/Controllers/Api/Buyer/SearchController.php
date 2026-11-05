<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{

    /**
     * Autocomplete search terms are derived only from active, non-archived
     * products of active sellers. No hardcoded keywords.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);
        $term = trim($validated['q']);

        if (mb_strlen($term) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $escaped = addcslashes($term, '%_\\');
        $products = Product::query()
            ->where('products.is_archived', false)
            ->where('products.status', 'active')
            ->whereHas('seller', fn ($q) => $q->where('registration_status', 'active'))
            ->where('products.name', 'like', '%' . $escaped . '%')
            ->select('products.name')
            ->selectRaw(
                'CASE WHEN LOWER(products.name) = LOWER(?) THEN 0 WHEN LOWER(products.name) LIKE LOWER(?) THEN 1 ELSE 2 END AS relevance_rank',
                [$term, $escaped . '%']
            )
            ->distinct()
            ->orderBy('relevance_rank')
            ->orderBy('products.name')
            ->limit(6)
            ->pluck('products.name');

        return response()->json(['suggestions' => $products->values()]);
    }

    public function index(Request $request): JsonResponse
    {
        $input = $request->validate([
            'q' => 'nullable|string|max:150',
            'subcategory' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'sort' => 'nullable|in:relevance,latest,top-sales,price-low,price-high',
            'page' => 'nullable|integer|min:1',
        ]);

        $term = trim($input['q'] ?? '');
        $matching = Product::query()
            ->where('products.is_archived', false)
            ->where('products.status', 'active')
            ->whereHas('seller', fn ($q) => $q->where('registration_status', 'active'));

        if ($term !== '') {
            // Search product name, description, main category, subcategory, and brand.
            $matching->where(function ($q) use ($term) {
                $q->where('products.name', 'like', "%{$term}%")
                  ->orWhere('products.description', 'like', "%{$term}%")
                  ->orWhere('products.category', 'like', "%{$term}%")
                  ->orWhereHas('productSpecifications', function ($spec) use ($term) {
                      $spec->whereIn(DB::raw('LOWER(`key`)'), ['subcategory', 'brand'])
                          ->where('value', 'like', "%{$term}%");
                  });
            });
        }

        // Facets always come from the actual search matches, not a static inventory list.
        $ids = (clone $matching)->select('products.id');
        $subcategories = DB::table('product_specifications')
            ->whereIn('product_id', clone $ids)
            ->whereRaw('LOWER(`key`) = ?', ['subcategory'])
            ->whereNotNull('value')->where('value', '<>', '')
            ->select('value')->distinct()->orderBy('value')->pluck('value');
        $brands = DB::table('product_specifications')
            ->whereIn('product_id', clone $ids)
            ->whereRaw('LOWER(`key`) = ?', ['brand'])
            ->whereNotNull('value')->where('value', '<>', '')
            ->select('value')->distinct()->orderBy('value')->pluck('value');
        $provinces = DB::table('sellers')
            ->whereIn('id', (clone $matching)->select('products.seller_id'))
            ->whereNotNull('province')->where('province', '<>', '')
            ->select('province')->distinct()->orderBy('province')->pluck('province');

        $relatedShops = (clone $matching)->with('seller')->latest('products.id')->limit(75)->get()
            ->unique('seller_id')->take(5)->map(fn (Product $p) => [
                'id' => $p->seller?->id,
                'name' => $p->seller?->store_name ?? 'ShopEase Seller',
                'province' => $p->seller?->province,
            ])->values();

        $query = clone $matching;
        foreach (['subcategory' => 'subcategory', 'brand' => 'brand'] as $param => $key) {
            if (!empty($input[$param])) {
                $value = $input[$param];
                $query->whereHas('productSpecifications', fn ($q) => $q
                    ->whereRaw('LOWER(`key`) = ?', [$key])->where('value', $value));
            }
        }
        if (!empty($input['province'])) {
            $query->whereHas('seller', fn ($q) => $q->where('province', $input['province']));
        }
        // Match the sale price displayed on the product cards.
        $finalPrice = '(products.price * (100 - COALESCE(products.sale_percentage, 0)) / 100)';
        if (isset($input['min_price'])) $query->whereRaw("{$finalPrice} >= ?", [$input['min_price']]);
        if (isset($input['max_price'])) $query->whereRaw("{$finalPrice} <= ?", [$input['max_price']]);
        if (isset($input['min_rating']) && (float) $input['min_rating'] > 0) {
            $query->whereRaw('(SELECT AVG(pr.rating) FROM product_reviews pr WHERE pr.product_id = products.id) >= ?', [$input['min_rating']]);
        }

        $query->with(['seller', 'productSpecifications'])
            ->withAvg('reviews as product_rating_average', 'rating')
            ->withCount('reviews as product_rating_count')
            ->withSum(['orderItems as sold_count' => fn ($items) => $items->whereHas(
                'order', fn ($orders) => $orders->whereIn('status', ['delivered', 'completed'])
            )], 'quantity');

        switch ($input['sort'] ?? 'relevance') {
            case 'latest': $query->orderByDesc('products.created_at'); break;
            case 'top-sales': $query->orderByDesc('sold_count'); break;
            case 'price-low': $query->orderByRaw("{$finalPrice} ASC"); break;
            case 'price-high': $query->orderByRaw("{$finalPrice} DESC"); break;
            default:
                if ($term !== '') {
                    $query->orderByRaw('CASE WHEN products.name LIKE ? THEN 0 WHEN products.name LIKE ? THEN 1 ELSE 2 END', [$term, "%{$term}%"]);
                }
                $query->orderByDesc('products.created_at');
        }
        $page = $query->orderByDesc('products.id')->paginate(20);

        return response()->json([
            'data' => $page->getCollection()->map(function (Product $product) {
                $photos = collect($product->photos ?? [])->filter()->map(function ($photo) {
                    if (preg_match('~^(https?://|data:)~', (string) $photo)) return $photo;
                    $path = ltrim(preg_replace('~^/?(?:storage/|public/)~', '', (string) $photo), '/');
                    return Storage::disk('public')->url($path);
                })->values();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'sale_percentage' => (int) $product->sale_percentage,
                    'sale_price' => (float) $product->sale_price,
                    'image_url' => $photos->first(),
                    'rating_average' => round((float) ($product->product_rating_average ?? 0), 1),
                    'sold_count' => (int) ($product->sold_count ?? 0),
                    'stock' => (int) $product->stock_quantity,
                    'seller' => [
                        'id' => $product->seller?->id,
                        'store_name' => $product->seller?->store_name ?? 'ShopEase Seller',
                        'province' => $product->seller?->province,
                    ],
                ];
            })->values(),
            'total' => $page->total(),
            'current_page' => $page->currentPage(),
            'last_page' => $page->lastPage(),
            'related_shops' => $relatedShops,
            'facets' => [
                'subcategories' => $subcategories,
                'brands' => $brands,
                'provinces' => $provinces,
            ],
        ]);
    }
}
