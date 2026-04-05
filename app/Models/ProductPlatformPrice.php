<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPlatformPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'platform_id',
        'total_deduction_percent',
        'fixed_fee_amount',
        'selling_price',
        'net_profit',
    ];

    protected function casts(): array
    {
        return [
            'total_deduction_percent' => 'decimal:2',
            'fixed_fee_amount' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'net_profit' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
