<?php

namespace Tests\Unit;

use App\Rules\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberRuleTest extends TestCase
{
    public function test_valid_vietnamese_phone_numbers_pass(): void
    {
        $rule = new PhoneNumber;

        $validPhones = [
            '0912345678',
            '0987654321',
            '0321234567',
            '0561234567',
            '0771234567',
            '0881234567',
            '0111111111',
            '+84912345678',
            '+84987654321',
            '+84321234567',
            ' 0912345678 ', // trimmed
        ];

        foreach ($validPhones as $phone) {
            $failed = false;
            $rule->validate('phone', $phone, function () use (&$failed) {
                $failed = true;
            });
            $this->assertFalse($failed, "Valid phone [{$phone}] was incorrectly marked as invalid.");
        }
    }

    public function test_invalid_phone_numbers_fail(): void
    {
        $rule = new PhoneNumber;

        $invalidPhones = [
            '123456789',       // missing leading 0 or +84
            '091234567',       // 9 digits (too short)
            '091234567890',    // 12 digits (too long)
            '091234567a',      // contains alphabet
            '+8412345678',     // too short
            '+8412345678901',  // too long
            '0912-345-678',    // contains dashes
            'abcxyz',          // non-numeric
            '',                // empty string
            '+123456789',      // non-VN country code
        ];

        foreach ($invalidPhones as $phone) {
            $failed = false;
            $rule->validate('phone', $phone, function () use (&$failed) {
                $failed = true;
            });
            $this->assertTrue($failed, "Invalid phone [{$phone}] was incorrectly marked as valid.");
        }
    }
}
