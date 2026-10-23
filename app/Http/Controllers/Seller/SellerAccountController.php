<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use App\Models\Admin\Registration;
use App\Models\User;
use App\Services\SellerNotificationPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Notifications\SellerCategoryChangeRequested;

class SellerAccountController extends Controller
{
    private const CATEGORIES = [
        'Pet Supplies', 'Electronics & Gadgets', "Women's Apparel", "Men's Apparel", 'Kids & Baby',
        'Home & Garden', 'Sports & Outdoors', 'Health & Beauty', 'Books & Media', 'Food & Gourmet',
        'Automotive & Motorcycle', 'Furniture & Office Equipment', 'Jewelry & Watches', 'Office & School Supplies',
    ];

    private function seller(Request $request): Seller
    {
        abort_unless($request->user() && $request->user()->role === User::ROLE_SELLER, 403);
        return Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function normalizeCategories(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : explode(',', $raw);
        }
        if (!is_array($raw)) return [];
        $map = [];
        foreach (self::CATEGORIES as $cat) {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(str_replace('&', 'and', $cat)));
            $map[trim($slug, '-')] = $cat;
        }
        $categories = [];
        foreach ($raw as $item) {
            $name = trim((string) (is_array($item) ? ($item['name'] ?? $item['label'] ?? '') : $item));
            $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower(str_replace('&', 'and', $name))), '-');
            $resolved = $map[$slug] ?? null;
            if ($resolved && !in_array($resolved, $categories, true)) $categories[] = $resolved;
        }
        return $categories;
    }

    private function categoriesFor(Seller $seller): array
    {
        // Registration saves categories to sellers.line_of_business.
        return $this->normalizeCategories($seller->line_of_business);
    }

    public function index(Request $request)
    {
        $seller = $this->seller($request);
        $registration = Registration::query()->where('user_id', $request->user()->id)->latest('id')->first();
        $pending = DB::table('seller_category_change_requests')
            ->where('seller_id', $seller->id)->where('status', 'pending')->first();
        return view('pages.seller.account', [
            'seller' => $seller,
            'registration' => $registration,
            'selectedCategories' => $this->categoriesFor($seller),
            'categoryOptions' => self::CATEGORIES,
            'validIdAvailable' => !empty($seller->upload_id),
            'permitAvailable' => !empty($seller->upload_business_permit),
            'pendingCategoryRequest' => $pending,
            'approvedPermits' => DB::table('seller_business_permits')->where('seller_id', $seller->id)->orderBy('id')->get()->all(),
            'pendingDocumentRequests' => DB::table('seller_document_change_requests')->where('seller_id', $seller->id)->where('status','pending')->pluck('id','request_type')->all(),
            'notificationPreferences' => app(SellerNotificationPreferences::class)->forUser($request->user()),
            'coverUrl' => $this->publicImageUrl($seller, 'cover_photo_path'),
            'avatarUrl' => $this->publicImageUrl($seller, 'profile_photo_path'),
        ]);
    }

    private function publicImageUrl(Seller $seller, string $column): ?string
    {
        if (!Schema::hasColumn($seller->getTable(), $column)) return null;
        $path = $seller->{$column};
        return is_string($path) && $path !== '' ? Storage::disk('public')->url($path) : null;
    }

    public function document(Request $request, string $type)
    {
        $seller = $this->seller($request);
        $column = match ($type) {
            'valid-id' => 'upload_id',
            'permit' => 'upload_business_permit',
            default => abort(404),
        };
        $path = $seller->{$column};
        abort_unless(is_string($path) && $path !== '' && Storage::disk('public')->exists($path), 404);
        // Owner-only authenticated preview; docs are not passed to arbitrary external URLs.
        $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true), 415);
        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="document.'.($mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg')).'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function update(Request $request)
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'last_name' => ['required','string','max:150'],
            'first_name' => ['required','string','max:150'],
            'middle_initial' => ['nullable','string','max:150'],
            'sex' => ['required', Rule::in(['Male','Female','Prefer not to say'])],
            'email' => ['required','email','max:255',Rule::unique('users','email')->ignore($request->user()->id)],
            'contact_no' => ['required','string','max:30'],
            'birthday' => ['required','date','before:today'],
            'province' => ['required','string','max:150'],
            'municipality' => ['required','string','max:150'],
            'barangay' => ['required','string','max:150'],
            'zip_code' => ['required','string','max:20'],
            'street' => ['nullable','string','max:150'],
            'house_number' => ['nullable','string','max:80'],
            'building' => ['nullable','string','max:150'],
            'subdivision' => ['nullable','string','max:150'],
            
            'cover_photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'profile_photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
        ]);
        if ($request->user()->email !== $request->input('email')) {
            throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'Email changes require verification. Please keep your current email for now.']);
        }
        // Do not allow direct editing of approved categories.
        unset($validated['email'], $validated['cover_photo'], $validated['profile_photo']);
        $columns = Schema::getColumnListing($seller->getTable());
        $validated['age'] = \Carbon\Carbon::parse($validated['birthday'])->age;
        $oldProfilePhotoPath = $seller->profile_photo_path;
        $newProfilePhotoPath = null;
        foreach ($validated as $field => $value) {
            if (in_array($field, $columns, true)) $seller->{$field} = $value;
        }
        foreach (['cover_photo'=>'cover_photo_path', 'profile_photo'=>'profile_photo_path'] as $input => $column) {
            if ($request->hasFile($input) && in_array($column, $columns, true)) {
                $newPath = $request->file($input)->store('seller-account/'.$seller->id, 'public');
                $seller->{$column} = $newPath;
                if ($input === 'profile_photo') {
                    $newProfilePhotoPath = $newPath;
                }
            }
        }
        DB::transaction(function () use ($seller, $request, $newProfilePhotoPath) {
            $seller->save();
            if ($newProfilePhotoPath) {
                $request->user()->profile_photo_path = $newProfilePhotoPath;
                $request->user()->save();
            }
        });
        if ($newProfilePhotoPath && $oldProfilePhotoPath) {
            Storage::disk('public')->delete($oldProfilePhotoPath);
        }
        return redirect()->route('seller.account')->with('success', 'Your profile information has been updated successfully.');
    }

    public function requestCategoryChange(Request $request)
    {
        $seller = $this->seller($request);
        $data = $request->validate([
            'requested_categories' => ['required','array','min:1','max:14'],
            'requested_categories.*' => ['required','string',Rule::in(self::CATEGORIES)],
            'reason' => ['required','string','min:10','max:1000'],
        ]);
        $current = $this->categoriesFor($seller);
        $requested = array_values(array_unique($data['requested_categories']));
        $a = $current; $b = $requested; sort($a); sort($b);
        if ($a === $b) return back()->withErrors(['requested_categories' => 'Select a different category set before requesting a change.']);
        $admins = User::query()->where('role', User::ROLE_ADMIN)->get();
        if ($admins->isEmpty()) return back()->withErrors(['requested_categories' => 'No admin account is available to receive this request.']);
        $newRequest = DB::transaction(function () use ($seller, $request, $current, $requested, $data) {
            $existing = DB::table('seller_category_change_requests')
                ->where('seller_id', $seller->id)->where('status', 'pending')->lockForUpdate()->exists();
            if ($existing) throw \Illuminate\Validation\ValidationException::withMessages(['requested_categories' => 'You already have a pending category-change request.']);
            $id = DB::table('seller_category_change_requests')->insertGetId([
                'seller_id' => $seller->id,
                'user_id' => $request->user()->id,
                'current_categories' => json_encode($current),
                'requested_categories' => json_encode($requested),
                'reason' => $data['reason'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return $id;
        });
        Notification::send($admins, new SellerCategoryChangeRequested($newRequest, $seller->store_name ?: 'Seller'));
        return redirect()->route('seller.account')->with('success', 'Your category change request was submitted to the admin successfully. Your registered categories remain unchanged while it is pending.');
    }

    public function approvedPermit(Request $request, int $permit)
    {
        $seller=$this->seller($request);
        $entry=DB::table('seller_business_permits')->where('id',$permit)->where('seller_id',$seller->id)->first();
        abort_unless($entry,404);
        return $this->servePrivateImage($entry->path);
    }

    private function servePrivateImage(string $path)
    {
        $disk=Storage::disk('public');
        abort_unless($disk->exists($path),404);
        $mime=$disk->mimeType($path);
        abort_unless(in_array($mime,['image/jpeg','image/png','image/webp'],true),415);
        return response($disk->get($path),200,[
            'Content-Type'=>$mime,'Content-Disposition'=>'inline',
            'Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff',
        ]);
    }

    public function requestDocumentChange(Request $request)
    {
        $seller=$this->seller($request);
        $data=$request->validate([
            'request_type'=>['required',Rule::in(['replace_valid_id','replace_business_permit','add_business_permit'])],
            'document'=>['required','image','mimes:jpeg,jpg,png,webp','max:5120'],
            'reason'=>['required','string','min:10','max:1000'],
        ]);
        $type=$data['request_type'];
        if ($type==='replace_valid_id' && !$seller->upload_id) return back()->withErrors(['document'=>'No existing Valid ID to replace. Please contact the administrator.']);
        if ($type==='replace_business_permit' && !$seller->upload_business_permit) return back()->withErrors(['document'=>'No original business permit to replace. Use Add Business Permit instead.']);
        if (DB::table('seller_document_change_requests')->where('seller_id',$seller->id)->where('request_type',$type)->where('status','pending')->exists())
            return back()->withErrors(['document'=>'You already have a pending request for this document type.']);
        $admins=User::where('role',User::ROLE_ADMIN)->get();
        if ($admins->isEmpty()) return back()->withErrors(['document'=>'No admin available to review this request.']);
        $path=$request->file('document')->store('seller-document-requests/'.$seller->id,'local');
        try {
            $id=DB::table('seller_document_change_requests')->insertGetId([
                'seller_id'=>$seller->id,'user_id'=>$request->user()->id,'request_type'=>$type,
                'document_path'=>$path,'original_name'=>$request->file('document')->getClientOriginalName(),
                'reason'=>$data['reason'],'status'=>'pending','created_at'=>now(),'updated_at'=>now(),
            ]);
        } catch (\Throwable $e) {Storage::disk('local')->delete($path);throw $e;}
        Notification::send($admins,new \App\Notifications\SellerDocumentChangeRequested($id,$seller->store_name ?: 'Seller',$type));
        return redirect()->route('seller.account')->with('success','Your document request was sent for admin approval. Your approved documents have not changed.');
    }

}
