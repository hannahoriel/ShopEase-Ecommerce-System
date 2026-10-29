<?php

namespace Database\Seeders;

use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsAccountChangeRequest;
use App\Models\Logistics\LogisticsAccountSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogisticsAccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'tracking.logistics@shopease.test')->first();
        $logistics = $user ? Logistics::query()->where('user_id', $user->id)->first() : null;
        if (! $user || ! $logistics) {
            return;
        }

        $details = $logistics->registration_details ?? [];
        $details['company'] = array_merge($details['company'] ?? [], [
            'company_name' => 'ShopEase Demo Logistics',
            'registered_business_name' => 'ShopEase Demo Logistics Corporation',
            'company_type' => 'Corporation',
            'sec_registration_number' => 'CS202612345',
            'tin' => '234-567-890-000',
            'company_email' => $user->email,
            'company_phone' => $logistics->contact_no,
            'website' => 'https://example.com',
            'branch_count' => '1',
            'years_in_operation' => '5',
            'representative_name' => 'Demo Logistics',
            'representative_position' => 'Operations Manager',
            'representative_email' => $user->email,
            'representative_phone' => $logistics->contact_no,
            'region' => 'Region IV-A',
            'province' => 'Laguna',
            'city' => 'Santa Cruz',
            'barangay' => 'Poblacion',
            'postal_code' => '4009',
            'street' => 'Demo Road',
            'house_no' => '1',
            'building' => 'Operations Center',
            'subdivision' => 'Demo Subdivision',
        ]);
        $logistics->forceFill(['registration_details' => $details])->save();

        LogisticsAccountSetting::query()->updateOrCreate(['user_id' => $user->id], [
            'privacy_settings' => ['public_company_contact' => false, 'share_delivery_updates' => true],
            'notification_preferences' => [
                'parcel_updates' => true, 'order_updates' => true, 'messages' => true,
                'delivery_exceptions' => true, 'compliance' => true, 'email_alerts' => true,
                'announcements' => true, 'promotions' => false,
            ],
        ]);

        LogisticsAccountChangeRequest::query()->firstOrCreate(
            ['user_id' => $user->id, 'section' => 'profile', 'status' => 'pending'],
            ['requested_data' => ['company_phone' => '09171112222', 'website' => 'https://logistics.example.test']]
        );
    }
}
