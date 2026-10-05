<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoLockOnExcessiveCancellationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_account_is_automatically_locked_when_reaching_failed_delivery_threshold(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create(['is_active' => true]);

        // Tạo 2 đơn hàng trước đó đã bị hủy khi đang giao hàng (bùng hàng/giao không thành công)
        $pastOrders = Order::factory()->count(2)->for($customer)->create([
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => now()->subDay(),
        ]);
        foreach ($pastOrders as $pastOrder) {
            $pastOrder->statusHistories()->create([
                'from_status' => 'delivering',
                'to_status' => 'cancelled',
                'reason' => 'Không liên lạc được khi giao',
            ]);
        }

        $this->assertTrue($customer->fresh()->is_active);

        // Đơn thứ 3 đang giao và tiếp tục bị bùng hàng
        $order3 = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Delivering,
        ]);

        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order3->id.'/status', [
            'status' => OrderStatus::Cancelled->value,
            'reason' => 'Khách bom hàng không nghe máy',
        ])->assertOk();

        // Kiểm tra tài khoản khách hàng đã tự động bị khóa do đủ 3 lần bùng hàng khi giao
        $customer->refresh();
        $this->assertFalse($customer->is_active);

        // Khách hàng bị khóa sẽ không thể truy cập các route yêu cầu active
        $this->actingAs($customer)->getJson('/api/v1/orders')
            ->assertForbidden();
    }

    public function test_cancelling_at_pending_stage_does_not_count_as_failed_delivery_violation(): void
    {
        $customer = User::factory()->create(['is_active' => true]);
        $service = app(OrderStatusService::class);

        // Khách hàng tự hủy 5 đơn lúc vừa đặt (ở bước pending)
        for ($i = 0; $i < 5; $i++) {
            $order = Order::factory()->for($customer)->create([
                'status' => OrderStatus::Pending,
            ]);

            $service->cancelByUser($order, 'Đổi ý không mua nữa', $customer);
        }

        // Tài khoản vẫn hoàn toàn hoạt động bình thường, không bị tính vi phạm bom hàng
        $customer->refresh();
        $this->assertTrue($customer->is_active);
    }

    public function test_admin_can_manually_unlock_account_and_reset_penalty_counter(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create([
            'is_active' => false,
        ]);

        // Tạo sẵn 3 đơn bị bùng khi giao
        $pastOrders = Order::factory()->count(3)->for($customer)->create([
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => now()->subDay(),
        ]);
        foreach ($pastOrders as $pastOrder) {
            $pastOrder->statusHistories()->create([
                'from_status' => 'delivering',
                'to_status' => 'cancelled',
                'reason' => 'Bom hàng',
            ]);
        }

        // 1. Admin mở khóa thủ công
        $response = $this->actingAs($admin)->patchJson('/api/v1/admin/users/'.$customer->id.'/status', [
            'is_active' => true,
        ]);

        $response->assertOk();
        $customer->refresh();
        $this->assertTrue($customer->is_active);
        $this->assertNotNull($customer->unlocked_at);

        // 2. Khách hàng đã mở khóa có thể truy cập lại bình thường
        $this->actingAs($customer)->getJson('/api/v1/orders')
            ->assertOk();

        // 3. Khách lỡ bị giao thất bại 1 đơn sau khi mở khóa -> chưa đạt ngưỡng 3 đơn mới nên chưa bị khóa lại
        $order = Order::factory()->for($customer)->create([
            'status' => OrderStatus::Delivering,
        ]);

        $this->actingAs($admin)->patchJson('/api/v1/admin/orders/'.$order->id.'/status', [
            'status' => OrderStatus::Cancelled->value,
            'reason' => 'Khách bận đi vắng',
        ])->assertOk();

        $customer->refresh();
        $this->assertTrue($customer->is_active);
    }
}
