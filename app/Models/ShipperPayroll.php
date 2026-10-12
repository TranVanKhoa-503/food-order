<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipperPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipper_id',
        'year',
        'month',
        'base_salary',
        'commission_rate',
        'completed_orders_count',
        'total_shipping_fee',
        'total_commission',
        'total_salary',
        'status',
        'finalized_at',
        'finalized_by',
        'paid_at',
        'paid_by',
        'note',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function shipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipper_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'base_salary' => 'decimal:0',
            'commission_rate' => 'decimal:2',
            'completed_orders_count' => 'integer',
            'total_shipping_fee' => 'decimal:0',
            'total_commission' => 'decimal:0',
            'total_salary' => 'decimal:0',
            'finalized_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }
}
