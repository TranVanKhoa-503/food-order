<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Support\CsvSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_sanitizer_unit_behavior(): void
    {
        // Normal values remain intact
        $this->assertSame('Nguyễn Văn A', CsvSanitizer::sanitize('Nguyễn Văn A'));
        $this->assertSame('0912345678', CsvSanitizer::sanitize('0912345678'));
        $this->assertSame(12345, CsvSanitizer::sanitize(12345));
        $this->assertSame('', CsvSanitizer::sanitize(''));
        $this->assertNull(CsvSanitizer::sanitize(null));

        // Dangerous formula prefixes are neutralized
        $this->assertSame("'=HYPERLINK(\"http://evil.com\")", CsvSanitizer::sanitize('=HYPERLINK("http://evil.com")'));
        $this->assertSame("'+123456789", CsvSanitizer::sanitize('+123456789'));
        $this->assertSame("'-1+1", CsvSanitizer::sanitize('-1+1'));
        $this->assertSame("'@SUM(A1:A2)", CsvSanitizer::sanitize('@SUM(A1:A2)'));

        // Dangerous values preceded by whitespace/control characters are neutralized
        $this->assertSame("'   =SUM(A1:A2)", CsvSanitizer::sanitize('   =SUM(A1:A2)'));
        $this->assertSame("'\t+84912345678", CsvSanitizer::sanitize("\t+84912345678"));
        $this->assertSame("'  @cmd", CsvSanitizer::sanitize('  @cmd'));
    }

    public function test_admin_can_export_csv_and_formula_injection_is_neutralized(): void
    {
        $admin = User::factory()->admin()->create();

        // Create an order with malicious injection attempts across user-controlled fields
        $order = Order::factory()->create([
            'order_code' => 'ORD-FORMULA-01',
            'customer_name' => '=CMD|/C calc!A0',
            'customer_phone' => '+123456789',
            'delivery_address' => '   =HYPERLINK("http://evil.com")',
            'status' => OrderStatus::Pending,
        ]);

        OrderItem::factory()->for($order)->create([
            'food_name' => '@SUM(1+1)',
            'quantity' => 2,
        ]);

        // Create a normal order to verify normal values remain correct
        $normalOrder = Order::factory()->create([
            'order_code' => 'ORD-NORMAL-02',
            'customer_name' => 'Nguyễn Văn Bình',
            'customer_phone' => '0901234567',
            'delivery_address' => '123 Đường Lê Lợi, Quận 1',
            'status' => OrderStatus::Pending,
        ]);

        OrderItem::factory()->for($normalOrder)->create([
            'food_name' => 'Phở Bò Đặc Biệt',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/orders/export');

        $response->assertOk()
            ->assertHeader('content-disposition');

        $csvContent = $response->streamedContent();

        // Malicious fields are neutralized with a leading single quote
        $this->assertStringContainsString("'=CMD|/C calc!A0", $csvContent);
        $this->assertStringContainsString("'+123456789", $csvContent);
        $this->assertStringContainsString("'   =HYPERLINK", $csvContent);
        $this->assertStringContainsString("'@SUM(1+1)", $csvContent);

        // Normal fields remain unchanged
        $this->assertStringContainsString('Nguyễn Văn Bình', $csvContent);
        $this->assertStringContainsString('0901234567', $csvContent);
        $this->assertStringContainsString('123 Đường Lê Lợi, Quận 1', $csvContent);
        $this->assertStringContainsString('Phở Bò Đặc Biệt', $csvContent);
    }

    public function test_values_starting_with_minus_are_neutralized_in_csv_export(): void
    {
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->create([
            'order_code' => 'ORD-MINUS-01',
            'customer_name' => '-1+1',
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->get('/admin/orders/export');
        $response->assertOk();

        $csvContent = $response->streamedContent();
        $this->assertStringContainsString("'-1+1", $csvContent);
    }

    public function test_guest_cannot_export_csv(): void
    {
        $response = $this->get('/admin/orders/export');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_export_csv(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/orders/export');
        $response->assertForbidden();
    }
}
