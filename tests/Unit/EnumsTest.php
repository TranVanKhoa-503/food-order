<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_foundation_enum_values_match_the_database_contract(): void
    {
        $this->assertSame(['user', 'admin'], array_column(UserRole::cases(), 'value'));
        $this->assertSame(
            ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled'],
            array_column(OrderStatus::cases(), 'value'),
        );
        $this->assertSame(['cod'], array_column(PaymentMethod::cases(), 'value'));
        $this->assertSame(['unpaid', 'paid'], array_column(PaymentStatus::cases(), 'value'));
    }
}
