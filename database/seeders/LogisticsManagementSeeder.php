<?php

namespace Database\Seeders;

use App\Models\Admin\Registration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogisticsManagementSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'email' => 'logistics.pending@shopease.test',
                'company' => 'Demo Logistics - Pending',
                'contact_first' => 'Paolo',
                'contact_last' => 'Reyes',
                'status' => 'pending',
            ],
            [
                'email' => 'logistics.active@shopease.test',
                'company' => 'Demo Logistics - Active',
                'contact_first' => 'Maria',
                'contact_last' => 'Santos',
                'status' => 'approved',
            ],
            [
                'email' => 'logistics.rejected@shopease.test',
                'company' => 'Demo Logistics - Rejected',
                'contact_first' => 'Jose',
                'contact_last' => 'Garcia',
                'status' => 'rejected',
            ],
            [
                'email' => 'logistics.pending.north@shopease.test',
                'company' => 'Northstar Delivery',
                'contact_first' => 'Leah',
                'contact_last' => 'Navarro',
                'status' => 'pending',
            ],
            [
                'email' => 'logistics.pending.south@shopease.test',
                'company' => 'Southline Couriers',
                'contact_first' => 'Ramon',
                'contact_last' => 'Dizon',
                'status' => 'pending',
            ],
            [
                'email' => 'logistics.active.swiftgo@shopease.test',
                'company' => 'SwiftGo Logistics',
                'contact_first' => 'Angela',
                'contact_last' => 'Mendoza',
                'status' => 'approved',
            ],
            [
                'email' => 'logistics.active.parcelpro@shopease.test',
                'company' => 'ParcelPro Express',
                'contact_first' => 'Miguel',
                'contact_last' => 'Torres',
                'status' => 'approved',
            ],
            [
                'email' => 'logistics.rejected.quickship@shopease.test',
                'company' => 'QuickShip Transport',
                'contact_first' => 'Nina',
                'contact_last' => 'Flores',
                'status' => 'rejected',
            ],
            [
                'email' => 'logistics.rejected.roadstar@shopease.test',
                'company' => 'Roadstar Logistics',
                'contact_first' => 'Carlo',
                'contact_last' => 'Bautista',
                'status' => 'rejected',
            ],
        ] as $application) {
            $this->seedApplication($application);
        }

        $company = Logistics::query()->where('business_name', 'Demo Logistics - Active')->firstOrFail();

        $branch = LogisticsBranch::updateOrCreate(
            ['logistics_id' => $company->id, 'name' => 'Metro Manila Hub'],
            [
                'address' => '123 Example Road, Quezon City, Metro Manila',
                'contact_person' => 'Maria Santos',
                'phone' => '09171234567',
            ]
        );

        LogisticsBranch::updateOrCreate(
            ['logistics_id' => $company->id, 'name' => 'Calamba Hub'],
            [
                'address' => '45 Sample Avenue, Calamba, Laguna',
                'contact_person' => 'Ana Cruz',
                'phone' => '09181234567',
            ]
        );

        foreach ([
            'SwiftGo Logistics' => ['name' => 'Makati Dispatch Hub', 'address' => '88 Sample Street, Makati, Metro Manila'],
            'ParcelPro Express' => ['name' => 'Cebu Operations Hub', 'address' => '21 Demo Road, Cebu City, Cebu'],
        ] as $companyName => $branchDetails) {
            $activeCompany = Logistics::query()->where('business_name', $companyName)->firstOrFail();
            LogisticsBranch::updateOrCreate(
                ['logistics_id' => $activeCompany->id, 'name' => $branchDetails['name']],
                [
                    'address' => $branchDetails['address'],
                    'contact_person' => $activeCompany->first_name.' '.$activeCompany->last_name,
                    'phone' => $activeCompany->contact_no,
                ]
            );
        }

        $riderUser = User::updateOrCreate(
            ['email' => 'rider.demo@shopease.test'],
            [
                'name' => 'Demo Rider',
                'password' => 'password',
                'role' => User::ROLE_RIDER,
                'registration_status' => 'active',
                'approved_at' => now()->subDays(10),
                'rejected_at' => null,
            ]
        );

        Rider::updateOrCreate(
            ['user_id' => $riderUser->id],
            [
                'logistics_branch_id' => $branch->id,
                'first_name' => 'Demo',
                'last_name' => 'Rider',
                'sex' => 'other',
                'contact_no' => '09191234567',
                'birthday' => '1995-04-12',
                'age' => 31,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Example',
                'street' => 'Rider Street',
                'house_number' => '10',
                'vehicle' => 'motorcycle',
                'plate_number' => 'DEMO 1234',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(10),
                'rejected_at' => null,
            ]
        );
    }

    private function seedApplication(array $application): void
    {
        $status = $application['status'];
        $reviewedAt = $status === 'pending' ? null : now()->subDays($status === 'approved' ? 8 : 3);
        $userStatus = $status === 'approved' ? 'active' : $status;

        $user = User::updateOrCreate(
            ['email' => $application['email']],
            [
                'name' => "{$application['contact_first']} {$application['contact_last']}",
                'password' => 'password',
                'role' => User::ROLE_LOGISTICS,
                'registration_status' => $userStatus,
                'approved_at' => $status === 'approved' ? $reviewedAt : null,
                'rejected_at' => $status === 'rejected' ? $reviewedAt : null,
            ]
        );

        $profile = Logistics::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $application['contact_first'],
                'last_name' => $application['contact_last'],
                'sex' => 'other',
                'contact_no' => '09171234567',
                'birthday' => '1988-06-15',
                'age' => 38,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Example',
                'street' => 'Logistics Avenue',
                'house_number' => '25',
                'business_name' => $application['company'],
                'registration_status' => $userStatus,
                'approved_at' => $status === 'approved' ? $reviewedAt : null,
                'rejected_at' => $status === 'rejected' ? $reviewedAt : null,
            ]
        );

        Registration::updateOrCreate(
            ['email' => $application['email']],
            [
                'user_type' => User::ROLE_LOGISTICS,
                'first_name' => $application['contact_first'],
                'last_name' => $application['contact_last'],
                'sex' => 'other',
                'birthdate' => '1988-06-15',
                'phone' => '09171234567',
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Example',
                'street' => 'Logistics Avenue',
                'house_no' => '25',
                'zip_code' => '1100',
                'business_name' => $application['company'],
                'business_permit_path' => 'demo/logistics-business-permit.pdf',
                'valid_id_path' => 'demo/logistics-valid-id.jpg',
                'status' => $status,
                'rejection_reason' => $status === 'rejected'
                    ? 'Insufficient delivery coverage or operational capacity.'
                    : null,
                'rejection_details' => $status === 'rejected'
                    ? 'Demo record for the rejected applications archive.'
                    : null,
                'reviewed_by' => User::query()->where('role', User::ROLE_ADMIN)->value('id'),
                'reviewed_at' => $reviewedAt,
                'user_id' => $user->id,
            ]
        );
    }
}
