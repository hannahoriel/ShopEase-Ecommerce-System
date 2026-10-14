<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    private const OPTIONS = [
        'registrations' => ['New Registrations', 'New user registration submissions requiring review'],
        'seller_compliance' => ['Seller Compliance', 'Permit and category change requests and seller warnings'],
        'complaints' => ['Complaints & Disputes', 'New cases and updates to active disputes'],
        'commissions' => ['Commissions', 'Commission transaction activity and changes'],
        'logistics' => ['Logistics', 'Delivery operations and logistics escalations'],
        'messages' => ['Messages', 'New buyer, seller and internal conversations'],
        'platform' => ['Platform Announcements', 'Maintenance and important platform notices'],
        'reports' => ['Reports', 'Scheduled reports and insights'],
    ];

    // The role assignments are provisional. Edit this map after access roles are finalized.
    private const ROLE_OPTIONS = [
        'admin' => ['registrations', 'seller_compliance', 'complaints', 'commissions', 'logistics', 'messages', 'platform', 'reports'],
        'super_admin' => ['registrations', 'seller_compliance', 'complaints', 'commissions', 'logistics', 'messages', 'platform', 'reports'],
        'registration_admin' => ['registrations', 'messages', 'platform'],
        'compliance_admin' => ['seller_compliance', 'complaints', 'messages', 'platform'],
        'finance_admin' => ['commissions', 'reports', 'platform'],
        'logistics_admin' => ['logistics', 'messages', 'platform'],
    ];

    private function admin(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'admin', 403);
        return $user;
    }

    private function profile(Request $request)
    {
        return DB::table('admin_account_profiles')->where('user_id', $this->admin($request)->id)->first();
    }

    private function role($user, $profile): string
    {
        // Not editable by the profile owner: the future access/roles module owns this value.
        return (string) (data_get($profile, 'access_role') ?: $user->role);
    }

    private function optionsFor(string $role): array
    {
        $keys = self::ROLE_OPTIONS[$role] ?? ['messages', 'platform'];
        return array_intersect_key(self::OPTIONS, array_flip($keys));
    }

    public function index(Request $request)
    {
        $user = $this->admin($request);
        $profile = $this->profile($request);
        $role = $this->role($user, $profile);
        $prefs = json_decode($profile->preferences ?? '{}', true);
        return view('pages.admin.account', [
            'adminProfile' => $profile,
            'adminRole' => $role,
            'roleLabel' => ucwords(str_replace(['_', '-'], ' ', $role)),
            'adminId' => 'ADM-'.str_pad((string)$user->id, 5, '0', STR_PAD_LEFT),
            'adminPhoto' => !empty($profile->profile_photo_path) ? Storage::disk('public')->url($profile->profile_photo_path) : null,
            'adminCover' => !empty($profile->cover_photo_path) ? Storage::disk('public')->url($profile->cover_photo_path) : null,
            'adminIdPhoto' => !empty($profile->admin_id_photo_path) ? Storage::disk('public')->url($profile->admin_id_photo_path) : null,
            'adminNotificationOptions' => $this->optionsFor($role),
            'adminPreferences' => is_array($prefs) ? $prefs : [],
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->admin($request);
        $data = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'birthday' => ['nullable','date','before_or_equal:today'],
            'sex' => ['nullable',Rule::in(['Male','Female','Prefer not to say'])],
            'contact_number' => ['nullable','regex:/^(9\\d{9}|09\\d{9})$/'],
            'profile_photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'cover_photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            // The Super Admin supplies the Admin ID at account creation.
            'admin_id_photo' => ['prohibited'],
        ]);
        $profile = $this->profile($request);
        $files = [];
        foreach (['profile_photo' => 'profile_photo_path','cover_photo'=>'cover_photo_path'] as $input => $column) {
            if ($request->hasFile($input)) {
                $files[$column] = $request->file($input)->store('admin-accounts/'.$user->id, 'public');
            }
        }
        $row = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birthday' => $data['birthday'] ?? null,
            'sex' => $data['sex'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'updated_at' => now(),
        ] + $files;
        DB::transaction(function () use ($user, $row) {
            if (DB::table('admin_account_profiles')->where('user_id', $user->id)->exists()) {
                DB::table('admin_account_profiles')->where('user_id', $user->id)->update($row);
            } else {
                DB::table('admin_account_profiles')->insert(['user_id'=>$user->id] + $row + ['created_at'=>now()]);
            }
            $user->name = trim($row['first_name'].' '.$row['last_name']);
            $user->save();
        });
        // Existing approved photos are only deleted AFTER a successful save.
        foreach (['profile_photo_path','cover_photo_path'] as $column) {
            if (isset($files[$column]) && !empty($profile->$column)) {
                Storage::disk('public')->delete($profile->$column);
            }
        }
        return redirect()->route('admin.account')->with('success', 'Admin profile changes saved successfully.');
    }

    public function notifications(Request $request)
    {
        $user = $this->admin($request);
        $profile = $this->profile($request);
        $keys = array_keys($this->optionsFor($this->role($user, $profile)));
        $selected = $request->input('preferences', []);
        if (!is_array($selected)) $selected = [];
        $current = json_decode($profile->preferences ?? '{}', true);
        if (!is_array($current)) $current = [];
        foreach ($keys as $key) $current[$key] = array_key_exists($key, $selected) && $selected[$key] == '1';
        DB::table('admin_account_profiles')->updateOrInsert(
            ['user_id'=>$user->id],
            ['preferences'=>json_encode($current), 'updated_at'=>now(), 'created_at'=>now()]
        );
        return redirect(route('admin.account').'#notifications')->with('success', 'Notification preferences saved.');
    }
}
