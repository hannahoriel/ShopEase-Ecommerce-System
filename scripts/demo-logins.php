<?php

// Run from the ShopEase application root. The export contains account metadata
// and omits passwords, tokens, uploaded documents, and transactional records.
require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

$profiles = [
    'logistics' => 'logistics',
    'sorting_center' => 'logistics_branches',
    'delivery_hub' => 'logistics_delivery_hubs',
    'buyer' => 'buyers',
    'seller' => 'sellers',
    'rider' => 'riders',
    'courier' => 'courier_registrations',
];
$fields = array_flip(explode(' ', 'id user_id logistics_id logistics_branch_id sorting_center_id logistics_delivery_hub_id first_name last_name sex birthday age contact_no phone province municipality city barangay street house_number house_no region postal_code name hub_name address contact_person manager_name manager_phone manager_email login_email email business_name store_name line_of_business vehicle plate_number registration_type assignment_region assignment_province assignment_barangay municipal_hub logistics_operation courier_company'));
$references = [
    'user_id' => 'users',
    'logistics_id' => 'logistics',
    'logistics_branch_id' => 'logistics_branches',
    'sorting_center_id' => 'logistics_branches',
    'logistics_delivery_hub_id' => 'logistics_delivery_hubs',
];
$mode = $argv[1] ?? '';
$path = $argv[2] ?? '';
if (! in_array($mode, ['export', 'import', 'test'], true) || $path === '') {
    fwrite(STDERR, "Usage: php scripts/demo-logins.php export|import|test /path/to/roster.json\n");
    exit(1);
}

if ($mode === 'export') {
    $emails = [];
    foreach (glob('database/seeders/*.php') as $file) {
        preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', file_get_contents($file), $matches);
        $emails = array_merge($emails, $matches[0]);
    }
    $users = DB::table('users')->where(
        fn ($query) => $query->where('email', 'like', '%@shopease.test')->orWhereIn('email', array_unique($emails))
    )->get(['id', 'email', 'name', 'role'])->map(fn ($row) => (array) $row)->all();
    $tables = [];
    foreach ($profiles as $role => $table) {
        $ids = array_column(array_filter($users, fn ($user) => $user['role'] === $role), 'id');
        $tables[$table] = DB::table($table)->whereIn('user_id', $ids)->orderBy('id')->get()
            ->map(fn ($row) => array_intersect_key((array) $row, $fields))->all();
    }
    $riderIds = array_column(array_filter($users, fn ($user) => $user['role'] === 'rider'), 'id');
    $tables['delivery_hub_rider_registrations'] = DB::table('delivery_hub_rider_registrations')
        ->whereIn('user_id', $riderIds)->orderBy('id')->get()
        ->map(fn ($row) => array_intersect_key((array) $row, $fields))->all();
    $out = fopen($path, 'x');
    fwrite($out, json_encode(['users' => $users, 'tables' => $tables], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    fclose($out);
    echo count($users)." demo accounts exported.\n";
    exit;
}

$data = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
if ($mode === 'test') {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.url' => null, 'database.connections.sqlite.database' => ':memory:']);
    DB::purge('sqlite');
    if (DB::connection()->getDatabaseName() !== ':memory:' || Artisan::call('migrate', ['--force' => true]) !== 0) {
        throw new RuntimeException('Could not initialize the in-memory database.');
    }
}

$credentials = DB::transaction(function () use ($data, $profiles, $references, $fields): array {
    $maps = [];
    $preserved = [];
    $credentials = [];
    $idsWithProfiles = [];
    $usersById = array_column($data['users'], null, 'id');
    $now = now();
    foreach ($data['users'] as $user) {
        if (! in_array($user['role'], [...array_keys($profiles), 'admin'], true) || ! filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The roster contains an invalid email or role.');
        }
        $existing = DB::table('users')->where('email', $user['email'])->lockForUpdate()->first();
        if ($existing && $existing->role !== $user['role']) {
            throw new RuntimeException("Email {$user['email']} already belongs to a different role.");
        }
        if ($existing) {
            $maps['users'][$user['id']] = $existing->id;
            $preserved[$user['id']] = true;
            if ($user['role'] !== 'admin' && $existing->registration_status !== 'active') {
                throw new RuntimeException("Existing account {$user['email']} is not active. Review it in Admin before importing.");
            }
            continue;
        }
        $password = bin2hex(random_bytes(18)).'Aa1!';
        $maps['users'][$user['id']] = DB::table('users')->insertGetId([
            'email' => $user['email'], 'name' => $user['name'], 'role' => $user['role'],
            'password' => Hash::make($password), 'email_verified_at' => $now,
            'registration_status' => 'active', 'approved_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $credentials[] = [$user['email'], $user['role'], $password];
    }

    foreach ([...array_values($profiles), 'delivery_hub_rider_registrations'] as $table) {
        $columns = array_flip(Schema::getColumnListing($table));
        foreach ($data['tables'][$table] ?? [] as $row) {
            $row = array_intersect_key($row, $fields);
            $oldId = $row['id'];
            $sourceUser = $row['user_id'];
            $userId = $maps['users'][$sourceUser] ?? throw new RuntimeException("No owner for {$table} row {$oldId}.");
            $idsWithProfiles[$table][$sourceUser] = true;
            if (isset($preserved[$sourceUser])) {
                $id = DB::table($table)->where('user_id', $userId)->value('id');
                if ($id !== null) {
                    $maps[$table][$oldId] = $id;
                    continue;
                }
            }

            unset($row['id']);
            foreach ($references as $column => $target) {
                if (isset($row[$column])) {
                    $mappedId = $maps[$target][$row[$column]] ?? null;
                    if ($mappedId === null && $target !== 'users' && ! empty($maps[$target])) {
                        $mappedId = reset($maps[$target]);
                        fwrite(STDERR, "Using a demo {$target} assignment for {$usersById[$sourceUser]['email']}.\n");
                    }
                    $row[$column] = $mappedId
                        ?? throw new RuntimeException("Missing {$target} assignment for {$usersById[$sourceUser]['email']}.");
                }
            }
            $row += ['created_at' => $now, 'updated_at' => $now, 'approved_at' => $now, 'reviewed_at' => $now];
            $row['registration_status'] = 'active';
            $row['status'] = in_array($table, ['courier_registrations', 'delivery_hub_rider_registrations'], true) ? 'approved' : 'active';
            $maps[$table][$oldId] = DB::table($table)->insertGetId(array_intersect_key($row, $columns));
        }
    }

    foreach ($data['users'] as $user) {
        if (! in_array($user['role'], ['buyer', 'seller'], true)) {
            continue;
        }
        $table = $profiles[$user['role']];
        $userId = $maps['users'][$user['id']];
        if (isset($idsWithProfiles[$table][$user['id']]) || DB::table($table)->where('user_id', $userId)->exists()) {
            continue;
        }
        $name = explode(' ', trim($user['name']), 2);
        $row = [
            'user_id' => $userId, 'first_name' => $name[0], 'last_name' => $name[1] ?? 'Demo',
            'sex' => 'other', 'birthday' => '1995-01-01', 'age' => 31, 'contact_no' => '00000000000',
            'province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros',
            'business_name' => $user['name'], 'store_name' => $user['name'], 'registration_status' => 'active',
            'approved_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ];
        DB::table($table)->insert(array_intersect_key($row, array_flip(Schema::getColumnListing($table))));
    }

    return $credentials;
});

echo count($data['users']).' accounts checked, '.count($credentials)." created.\n";
if ($mode === 'import') {
    foreach ($credentials as [$email, $role, $password]) {
        echo "{$email}\t{$role}\t{$password}\n";
    }
}
