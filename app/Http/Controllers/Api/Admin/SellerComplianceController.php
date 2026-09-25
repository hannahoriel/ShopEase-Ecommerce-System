<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerComplianceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SELLER COMPLIANCE LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'compliance' => [
                'nullable',
                'in:compliant,warning,under-review,suspended',
            ],

            'status' => [
                'nullable',
                'string',
                'max:30',
            ],

            'from' => [
                'nullable',
                'date',
            ],

            'to' => [
                'nullable',
                'date',
                'after_or_equal:from',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);


        $sellers = Seller::query()
            ->with([
                'user',

                'products' => fn ($query) =>
                    $query->where(
                        'is_archived',
                        false
                    ),
            ])

            ->when(
                $validated['search'] ?? null,
                function ($query, string $search): void {

                    $query->where(function ($query) use ($search): void {

                        $query
                            ->where(
                                'store_name',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'business_name',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'first_name',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'last_name',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'contact_no',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhereHas(
                                'user',
                                fn ($user) =>
                                    $user->where(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    )
                            );

                    });

                }
            )

            ->when(
                $validated['from'] ?? null,
                fn ($query, string $from) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $from
                    )
            )

            ->when(
                $validated['to'] ?? null,
                fn ($query, string $to) =>
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $to
                    )
            )

            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orderIdsBySeller = Order::query()
            ->whereIn(
                'seller_id',
                $sellers->pluck('id')
            )
            ->get([
                'id',
                'seller_id',
            ])
            ->groupBy('seller_id');


        /*
        |--------------------------------------------------------------------------
        | COMPLAINTS
        |--------------------------------------------------------------------------
        */

        $complaintsByOrder = Complaint::query()
            ->whereIn(
                'order_id',
                $orderIdsBySeller
                    ->flatten()
                    ->pluck('id')
            )

            ->whereIn(
                'status',
                [
                    'open',
                    'in_progress',
                ]
            )

            ->get([
                'order_id',
            ])

            ->groupBy('order_id');


        /*
        |--------------------------------------------------------------------------
        | BUILD SELLER ROWS
        |--------------------------------------------------------------------------
        */

        $rows = $sellers->map(
            function (Seller $seller) use (
                $orderIdsBySeller,
                $complaintsByOrder
            ): array {

                $reviewProducts =
                    $seller->products->filter(
                        fn ($product): bool =>
                            in_array(
                                strtolower(
                                    (string) $product->status
                                ),
                                [
                                    'pending',
                                    'under_review',
                                    'review',
                                ],
                                true
                            )
                    );


                $orderIds =
                    $orderIdsBySeller
                        ->get(
                            $seller->id,
                            collect()
                        )
                        ->pluck('id');


                $complaintCount =
                    $orderIds->sum(
                        fn ($orderId): int =>
                            $complaintsByOrder
                                ->get(
                                    $orderId,
                                    collect()
                                )
                                ->count()
                    );


                $compliance =
                    $this->complianceFor(
                        $seller,
                        $reviewProducts->count(),
                        $complaintCount
                    );


                $categories =
                    $seller->products
                        ->pluck('category')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();


                return [

                    'id' =>
                        $seller->id,

                    'store_name' =>
                        $seller->store_name
                        ?: $seller->business_name,

                    'owner_name' =>
                        trim(
                            "{$seller->first_name} {$seller->last_name}"
                        ),

                    'email' =>
                        $seller->user?->email,

                    'phone' =>
                        $seller->contact_no,

                    'categories' =>
                        $categories,

                    'products_count' =>
                        $seller->products->count(),

                    'products_under_review' =>
                        $reviewProducts->count(),

                    'complaints_open' =>
                        $complaintCount,

                    'compliance_score' =>
                        $compliance['score'],

                    'compliance' =>
                        $compliance['status'],

                    'compliance_label' =>
                        $compliance['label'],

                    'status' =>
                        $seller->registration_status,

                    'created_at' =>
                        $seller->created_at?->toISOString(),

                    'seller_since' =>
                        $seller->created_at?->format('F Y'),

                    'location' =>
                        collect([
                            $seller->municipality,
                            $seller->province,
                        ])
                            ->filter()
                            ->implode(', '),

                    'valid_id_url' =>
                        $this->documentUrl(
                            $seller->upload_id
                        ),

                    'business_permit_url' =>
                        $this->documentUrl(
                            $seller->upload_business_permit
                        ),

                    'valid_id_name' =>
                        $seller->upload_id
                            ? basename(
                                $seller->upload_id
                            )
                            : null,

                    'business_permit_name' =>
                        $seller->upload_business_permit
                            ? basename(
                                $seller->upload_business_permit
                            )
                            : null,

                    'products' =>
                        $seller->products
                            ->map(
                                fn (Product $product): array =>
                                    $this->productPayload(
                                        $product
                                    )
                            )
                            ->values(),

                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $validated['category'] ?? null
        ) {

            $rows =
                $rows->filter(
                    fn (array $row): bool =>
                        in_array(
                            $validated['category'],
                            $row['categories'],
                            true
                        )
                );

        }


        /*
        |--------------------------------------------------------------------------
        | COMPLIANCE FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $validated['compliance'] ?? null
        ) {

            $rows =
                $rows->filter(
                    fn (array $row): bool =>
                        $row['compliance']
                        === $validated['compliance']
                );

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] ?? null
        ) {

            $rows =
                $rows->filter(
                    fn (array $row): bool =>
                        $row['status']
                        === $validated['status']
                );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage =
            $validated['per_page']
            ?? 7;


        $page =
            max(
                1,
                (int) $request->input(
                    'page',
                    1
                )
            );


        $total =
            $rows->count();


        $items =
            $rows
                ->forPage(
                    $page,
                    $perPage
                )
                ->values();


        return response()->json([

            'summary' => [

                'compliant' =>
                    $rows
                        ->where(
                            'compliance',
                            'compliant'
                        )
                        ->count(),

                'warnings' =>
                    $rows
                        ->where(
                            'compliance',
                            'warning'
                        )
                        ->count(),

                'under_review' =>
                    $rows
                        ->where(
                            'compliance',
                            'under-review'
                        )
                        ->count(),

                'suspended' =>
                    $rows
                        ->where(
                            'compliance',
                            'suspended'
                        )
                        ->count(),

                'total_sellers' =>
                    $rows->count(),

            ],

            'data' =>
                $items,

            'meta' => [

                'current_page' =>
                    $page,

                'per_page' =>
                    $perPage,

                'total' =>
                    $total,

                'last_page' =>
                    max(
                        1,
                        (int) ceil(
                            $total / $perPage
                        )
                    ),

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER COMPLIANCE PAGE
    |--------------------------------------------------------------------------
    */

    public function page(Request $request)
    {
        $initialComplianceData =
            $this->index($request)
                ->getData(true);


        return view(
            'pages.admin.SellerCompliance.seller-compliance',
            compact(
                'initialComplianceData'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW SELLER
    |--------------------------------------------------------------------------
    */

    public function show(Seller $seller)
    {
        $seller->load([
            'user',

            'products' => fn ($query) =>
                $query->where(
                    'is_archived',
                    false
                ),
        ]);


        $orderIds =
            Order::where(
                'seller_id',
                $seller->id
            )
            ->pluck('id');


        $complaintCount =
            Complaint::whereIn(
                'order_id',
                $orderIds
            )

            ->whereIn(
                'status',
                [
                    'open',
                    'in_progress',
                ]
            )

            ->count();


        $reviewCount =
            $seller->products
                ->filter(
                    fn ($product): bool =>
                        in_array(
                            strtolower(
                                (string) $product->status
                            ),
                            [
                                'pending',
                                'under_review',
                                'review',
                            ],
                            true
                        )
                )
                ->count();


        return response()->json([

            'data' =>
                $this->sellerPayload(
                    $seller,
                    $reviewCount,
                    $complaintCount
                ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function approveProduct(Product $product)
    {
        $allowedStatuses = [
            'pending',
            'under_review',
            'review',
        ];


        abort_unless(
            in_array(
                strtolower(
                    (string) $product->status
                ),
                $allowedStatuses,
                true
            ),
            422,
            'This product cannot be approved in its current status.'
        );


        $product->update([
            'status' =>
                'active',

            'warning_reason' =>
                null,

            'warning_details' =>
                null,

            'archive_reason' =>
                null,

            'is_archived' =>
                false,
        ]);


        return response()->json([

            'data' =>
                $product->fresh(),

            'message' =>
                'Product approved and now available in inventory.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | WARN PRODUCT
    |--------------------------------------------------------------------------
    */

    public function warnProduct(
        Request $request,
        Product $product
    ) {

        $validated =
            $request->validate([

                'reason' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'details' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

            ]);


        $allowedStatuses = [
            'pending',
            'under_review',
            'review',
            'warning',
        ];


        abort_unless(
            in_array(
                strtolower(
                    (string) $product->status
                ),
                $allowedStatuses,
                true
            ),
            422,
            'This product cannot be warned in its current status.'
        );


        $product->update([

            'status' =>
                'warning',

            'is_archived' =>
                false,

            'warning_reason' =>
                $validated['reason'],

            'warning_details' =>
                $validated['details'] ?? '',

            'archive_reason' =>
                null,

        ]);


        return response()->json([

            'data' =>
                $product->fresh(),

            'message' =>
                'Product warning issued and saved.',

            'reason' =>
                $validated['reason'],

            'details' =>
                $validated['details'] ?? '',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function removeProduct(
        Request $request,
        Product $product
    ) {

        $validated =
            $request->validate([

                'reason' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'details' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

            ]);


        $product->update([

            'status' =>
                'archived',

            'is_archived' =>
                true,

            'archived_by_admin' =>
                true,

            'archive_reason' =>
                $validated['reason'],

        ]);


        return response()->json([

            'data' =>
                $product->fresh(),

            'message' =>
                'Product removed and archived.',

            'reason' =>
                $validated['reason'],

            'details' =>
                $validated['details'] ?? '',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER PAYLOAD
    |--------------------------------------------------------------------------
    */

    private function sellerPayload(
        Seller $seller,
        int $reviewCount,
        int $complaintCount
    ): array {

        $compliance =
            $this->complianceFor(
                $seller,
                $reviewCount,
                $complaintCount
            );


        return [

            'id' =>
                $seller->id,

            'store_name' =>
                $seller->store_name
                ?: $seller->business_name,

            'owner_name' =>
                trim(
                    "{$seller->first_name} {$seller->last_name}"
                ),

            'email' =>
                $seller->user?->email,

            'phone' =>
                $seller->contact_no,

            'categories' =>
                $seller->products
                    ->pluck('category')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),

            'products_count' =>
                $seller->products->count(),

            'products_under_review' =>
                $reviewCount,

            'complaints_open' =>
                $complaintCount,

            'compliance_score' =>
                $compliance['score'],

            'compliance' =>
                $compliance['status'],

            'compliance_label' =>
                $compliance['label'],

            'status' =>
                $seller->registration_status,

            'seller_since' =>
                $seller->created_at?->format(
                    'F Y'
                ),

            'location' =>
                collect([
                    $seller->municipality,
                    $seller->province,
                ])
                    ->filter()
                    ->implode(', '),

            'valid_id_url' =>
                $this->documentUrl(
                    $seller->upload_id
                ),

            'business_permit_url' =>
                $this->documentUrl(
                    $seller->upload_business_permit
                ),

            'valid_id_name' =>
                $seller->upload_id
                    ? basename(
                        $seller->upload_id
                    )
                    : null,

            'business_permit_name' =>
                $seller->upload_business_permit
                    ? basename(
                        $seller->upload_business_permit
                    )
                    : null,

            'products' =>
                $seller->products
                    ->map(
                        fn (Product $product): array =>
                            $this->productPayload(
                                $product
                            )
                    )
                    ->values(),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | COMPLIANCE CALCULATION
    |--------------------------------------------------------------------------
    */

    private function complianceFor(
        Seller $seller,
        int $reviewCount,
        int $complaintCount
    ): array {

        if (
            in_array(
                $seller->registration_status,
                [
                    'suspended',
                    'rejected',
                ],
                true
            )
        ) {

            return [

                'status' =>
                    'suspended',

                'label' =>
                    'Suspended',

                'score' =>
                    0,

            ];
        }


        $score =
            max(
                0,
                100
                - ($reviewCount * 5)
                - ($complaintCount * 10)
            );


        if (
            $seller->registration_status
            === 'pending'
        ) {

            return [

                'status' =>
                    'under-review',

                'label' =>
                    'Under Review',

                'score' =>
                    $score,

            ];
        }


        if (
            $reviewCount > 0
        ) {

            return [

                'status' =>
                    'under-review',

                'label' =>
                    'Warning',

                'score' =>
                    $score,

            ];
        }


        if (
            $complaintCount > 0
            || $score < 80
        ) {

            return [

                'status' =>
                    'warning',

                'label' =>
                    'Warning',

                'score' =>
                    $score,

            ];
        }


        return [

            'status' =>
                'compliant',

            'label' =>
                'Compliant',

            'score' =>
                $score,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT URL
    |--------------------------------------------------------------------------
    */

    private function documentUrl(
        ?string $path
    ): ?string {

        return $path
            ? Storage::disk('public')->url($path)
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT PAYLOAD
    |--------------------------------------------------------------------------
    */

    private function productPayload(
        Product $product
    ): array {

        $payload =
            $product->toArray();


        $photos =
            collect(
                $product->photos ?? []
            )
            ->map(
                function ($photo) {
                    if (!is_string($photo)) {
                        return null;
                    }

                    $value = trim($photo);

                    if ($value === '') {
                        return null;
                    }

                    if (str_starts_with($value, 'data:') || str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                        return $value;
                    }

                    $relativePath = ltrim($value, '/');
                    $relativePath = preg_replace('/^storage\//', '', $relativePath);

                    if ($relativePath === '') {
                        return null;
                    }

                    return Storage::disk('public')->url($relativePath);
                }
            )
            ->filter()
            ->values()
            ->all();


        $payload['photos'] =
            $photos;


        $payload['image_url'] =
            $photos[0] ?? null;


        $payload['sold_count'] =
            0;


        $payload['variations'] =
            $product->variations ?? [];


        $payload['colors'] =
            $product->colors ?? [];


        $payload['sizes'] =
            $product->sizes ?? [];


        $payload['specifications'] =
            $product->specifications ?? [];


        return $payload;
    }
}
