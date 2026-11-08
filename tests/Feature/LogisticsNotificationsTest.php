<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class LogisticsNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'notification_test', 'database.connections.notification_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ], 'session.driver' => 'array']);
        DB::purge('notification_test');
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        foreach (['logistics' => 'business_name', 'logistics_branches' => 'name', 'logistics_delivery_hubs' => 'hub_name'] as $name => $field) {
            Schema::create($name, function (Blueprint $table) use ($field) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string($field);
            });
        }
    }

    private function account(int $id, string $role = 'logistics'): User
    {
        $user = new User;
        $user->forceFill(['id' => $id, 'role' => $role, 'name' => 'Logistics Partner']);
        return $user;
    }

    private function notification(User $user, string $title, bool $read = false)
    {
        return $user->notifications()->create([
            'id' => (string) Str::uuid(), 'type' => 'AppNotifications',
            'data' => ['title' => $title, 'message' => $title], 'read_at' => $read ? now() : null,
        ]);
    }

    public function test_page_shows_only_company_inbox_and_supports_read_filter(): void
    {
        $user = $this->account(10);
        $this->notification($user, 'Own unread notice');
        $this->notification($user, 'Own archived notice', true);
        $this->notification($this->account(20), 'Private other company notice');
        $this->actingAs($user)->get(route('logistics.notifications'))
            ->assertOk()->assertSee('Own unread notice')->assertSee('Own archived notice')
            ->assertDontSee('Private other company notice');
        $sidebar = view('components.logistics.sidebar')->render();
        $this->assertMatchesRegularExpression('/<a\b[^>]*href="'.preg_quote(route('logistics.notifications'), '/').'"[^>]*>.*?Notifications\s*<\/span>/s', $sidebar);
        $this->get(route('logistics.notifications', ['filter' => 'unread']))
            ->assertOk()->assertSee('Own unread notice')->assertDontSee('Own archived notice');
    }

    public function test_mark_read_actions_cannot_change_another_company_inbox(): void
    {
        $user = $this->account(10);
        $own = $this->notification($user, 'Own notice');
        $other = $this->notification($this->account(20), 'Private notice');
        $this->actingAs($user)->post(route('logistics.notifications.read', $other->id))->assertNotFound();
        $this->post(route('logistics.notifications.read', $own->id))->assertRedirect();
        $this->assertNotNull($own->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
        $this->notification($user, 'Another own notice');
        $this->post(route('logistics.notifications.read-all'))->assertRedirect();
        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertNull($other->fresh()->read_at);
    }

    public function test_other_roles_cannot_access_company_notifications(): void
    {
        $this->actingAs($this->account(30, 'buyer'))->get(route('logistics.notifications'))->assertForbidden();
    }

    public function test_shared_navbars_use_entity_names_and_private_notification_dropdowns(): void
    {
        foreach (['logistics' => ['logistics', 'business_name'], 'sorting-center' => ['logistics_branches', 'name'], 'delivery-hub' => ['logistics_delivery_hubs', 'hub_name']] as $area => [$table, $field]) {
            $user = $this->account(50);
            DB::table($table)->insert(['user_id' => 50, $field => 'Registered '.$area.' Name']);
            $this->notification($user, 'My unread update');
            $this->notification($this->account(60), 'Another private inbox');
            $this->actingAs($user);
            $html = view('components.logistics.shared-navbar', ['navbarArea' => $area])->render();
            $this->assertStringContainsString('Registered '.$area.' Name', $html);
            $this->assertStringContainsString('id="lgNotifyMenu"', $html);
            $this->assertStringContainsString('My unread update', $html);
            $this->assertStringNotContainsString('Another private inbox', $html);
            $this->assertStringContainsString('M19 9l-7 7-7-7', $html);
        }
    }
}
