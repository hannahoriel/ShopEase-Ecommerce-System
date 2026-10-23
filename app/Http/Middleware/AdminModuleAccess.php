<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminModuleAccess
{
    private const MODULE_PREFIXES = [
        'Dashboard' => ['admin/dashboard', 'api/v1/admin/dashboard'],
        'Registrations' => ['admin/registrations', 'api/v1/admin/registrations'],
        'User Management' => ['admin/user-management', 'api/v1/admin/users'],
        'Seller Compliance' => ['admin/seller-compliance', 'api/v1/admin/seller-compliance'],
        'Complaints and Disputes' => ['admin/complaints-disputes', 'admin/complaints', 'api/v1/admin/complaints'],
        'Commission' => ['admin/commission', 'api/v1/admin/commissions'],
        'Logistics Management' => ['admin/logistics-management', 'api/v1/admin/logistics-management'],
        'Reports' => ['admin/reports', 'api/v1/admin/reports'],
        'Platform Settings' => ['admin/platform-settings', 'api/v1/admin/platform-settings'],
        'Notifications' => ['admin/notifications', 'api/v1/admin/notifications'],
        'Account Management' => ['admin/account', 'api/v1/admin/account'],
        'Messages' => ['admin/messages', 'api/v1/admin/messages'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $path = $request->path();
        $module = $this->moduleForPath($path);

        if ($user?->role === User::ROLE_ADMIN) {
            $profile = DB::table('admin_account_profiles')
                ->where('user_id', $user->id)
                ->first(['permissions', 'is_enabled']);

            abort_if($profile && ! $profile->is_enabled, 403, 'This administrator account is disabled.');

            $permissions = $profile?->permissions === null
                ? null
                : json_decode($profile->permissions, true);
            $isFullAdmin = $profile === null || $profile->permissions === null;
            $canManageModule = $isFullAdmin
                || ($module !== null && is_array($permissions) && in_array($module, $permissions, true));

            $request->attributes->set('adminModuleAccess', [
                'module' => $module,
                'canManage' => $canManageModule,
                'readOnly' => ! $canManageModule,
            ]);

            if (! $isFullAdmin && $this->isAdminPath($path) && ! $request->isMethodSafe()) {
                abort_unless($canManageModule, 403, 'You do not have permission to edit this admin module.');
            }
        }

        return $next($request);
    }

    private function isAdminPath(string $path): bool
    {
        return $path === 'admin'
            || Str::startsWith($path, 'admin/')
            || $path === 'api/v1/admin'
            || Str::startsWith($path, 'api/v1/admin/');
    }

    private function moduleForPath(string $path): ?string
    {
        foreach (self::MODULE_PREFIXES as $module => $prefixes) {
            foreach ($prefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                    return $module;
                }
            }
        }

        return null;
    }
}
