<?php

use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\SortingCenterVehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorting_center_vehicles', function (Blueprint $table): void {
            $table->foreignId('courier_registration_id')
                ->nullable()
                ->after('sorting_center_id')
                ->constrained('courier_registrations')
                ->nullOnDelete();
        });

        SortingCenterVehicle::query()->whereNotNull('driver_name')->each(function (SortingCenterVehicle $vehicle): void {
            $driverName = mb_strtolower(trim((string) $vehicle->driver_name));
            $courier = CourierRegistration::query()
                ->where('sorting_center_id', $vehicle->sorting_center_id)
                ->where('status', 'approved')
                ->get()
                ->first(fn (CourierRegistration $candidate): bool => mb_strtolower(trim(implode(' ', array_filter([
                    $candidate->first_name,
                    $candidate->middle_name,
                    $candidate->last_name,
                ])))) === $driverName);

            if ($courier) {
                $vehicle->update(['courier_registration_id' => $courier->id]);
            }
        });

        CourierRegistration::query()->update(['vehicle' => null, 'plate_number' => null]);
    }

    public function down(): void
    {
        Schema::table('sorting_center_vehicles', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('courier_registration_id');
        });
    }
};
