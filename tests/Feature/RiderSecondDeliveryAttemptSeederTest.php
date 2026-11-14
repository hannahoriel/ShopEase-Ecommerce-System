<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Rider\DeliveryAttempt;
use App\Models\User;
use Database\Seeders\RiderSecondDeliveryAttemptSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RiderSecondDeliveryAttemptSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_parcel_accepts_attempt_two_and_seeder_reruns_preserve_progress(): void
    {
        Storage::fake('local');
        $this->seed(RiderSecondDeliveryAttemptSeeder::class);

        $order = Order::query()
            ->where('order_number', 'SE-RIDER-ATTEMPT2-DEMO-0001')
            ->firstOrFail();
        $this->assertSame('out_for_delivery', $order->status);
        $this->assertSame(1, $order->deliveryAttempts()->count());
        $this->assertDatabaseHas('rider_delivery_attempts', [
            'order_id' => $order->id,
            'attempt_number' => 1,
        ]);

        $rider = User::query()->where('email', 'rider@gmail.com')->firstOrFail();
        $this->assertDatabaseHas('riders', [
            'user_id' => $rider->id,
            'province' => 'Laguna',
            'municipality' => 'Santa Cruz',
            'barangay' => 'Bubukal',
            'assignment_region' => 'Calabarzon',
            'assignment_province' => 'Laguna',
            'assignment_barangay' => 'Bubukal',
        ]);

        $this->actingAs($rider)
            ->postJson('/api/v1/rider/deliveries/'.$order->id.'/failed-attempts', [
                'reason' => 'Buyer did not answer',
                'proof_photo' => UploadedFile::fake()->image('attempt-2.jpg'),
            ])
            ->assertCreated()
            ->assertJsonPath('data.attempt', 2);

        $this->seed(RiderSecondDeliveryAttemptSeeder::class);

        $this->assertDatabaseCount('rider_delivery_attempts', 2);
        $this->assertDatabaseHas('rider_delivery_attempts', [
            'order_id' => $order->id,
            'attempt_number' => 2,
            'reason' => 'Buyer did not answer',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'out_for_delivery',
        ]);
    }
}
