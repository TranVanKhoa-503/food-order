<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'amount',
        'expense_date',
        'description',
        'created_by',
    ];

    public const CATEGORIES = [
        'rent' => 'Mặt bằng / Thuê nhà',
        'utilities' => 'Điện nước / Internet',
        'marketing' => 'Quảng cáo / Marketing',
        'packaging' => 'Bao bì / Đóng gói',
        'equipment' => 'Dụng cụ / Thiết bị',
        'salary_other' => 'Lương nhân sự khác',
        'maintenance' => 'Bảo trì / Sửa chữa',
        'other' => 'Chi phí khác',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:0',
            'expense_date' => 'date',
        ];
    }
}
