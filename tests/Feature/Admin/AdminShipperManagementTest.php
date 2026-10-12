<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShipperManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_shippers_list(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create([
            'name' => 'Shipper Nguyễn Văn A',
            'phone' => '0912345678',
        ]);

        $this->actingAs($admin)->get('/admin/shippers')
            ->assertOk()
            ->assertSee('Shipper Nguyễn Văn A')
            ->assertSee('0912345678');

        $this->actingAs($admin)->getJson('/api/v1/admin/shippers')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Shipper Nguyễn Văn A');
    }

    public function test_admin_can_assign_shipper_to_order(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'shipper_id' => null,
        ]);

        $response = $this->actingAs($admin)->postJson("/api/v1/admin/orders/{$order->id}/assign-shipper", [
            'shipper_id' => $shipper->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.shipper_id', $shipper->id);

        $order->refresh();
        $this->assertEquals($shipper->id, $order->shipper_id);
        $this->assertNotNull($order->assigned_at);
    }

    public function test_admin_cannot_assign_inactive_shipper(): void
    {
        $admin = User::factory()->admin()->create();
        $inactiveShipper = User::factory()->shipper()->create(['is_active' => false]);
        $order = Order::factory()->create(['status' => OrderStatus::Confirmed]);

        $response = $this->actingAs($admin)->postJson("/api/v1/admin/orders/{$order->id}/assign-shipper", [
            'shipper_id' => $inactiveShipper->id,
        ]);

        $response->assertUnprocessable();
    }

    public function test_admin_cannot_assign_shipper_to_completed_or_cancelled_order(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();
        $completedOrder = Order::factory()->create(['status' => OrderStatus::Completed]);

        $response = $this->actingAs($admin)->postJson("/api/v1/admin/orders/{$completedOrder->id}/assign-shipper", [
            'shipper_id' => $shipper->id,
        ]);

        $response->assertUnprocessable();
    }

    public function test_shipper_cannot_update_order_assigned_to_another_shipper(): void
    {
        $shipper1 = User::factory()->shipper()->create();
        $shipper2 = User::factory()->shipper()->create();

        $order = Order::factory()->create([
            'status' => OrderStatus::Delivering,
            'shipper_id' => $shipper1->id,
        ]);

        $response = $this->actingAs($shipper2)->patchJson("/api/v1/shipper/orders/{$order->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertForbidden();
    }
}
