<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'buy_price',
        'additional_cost',
        'margin_type',
        'margin_value',
        'base_cost',
        'target_profit',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'additional_cost' => 'decimal:2',
            'margin_value' => 'decimal:2',
            'base_cost' => 'decimal:2',
            'target_profit' => 'decimal:2',
        ];
    }

    public function platformPrices(): HasMany
    {
        return $this->hasMany(ProductPlatformPrice::class);
    }

    public function additionalCostItems(): HasMany
    {
        return $this->hasMany(ProductAdditionalCostItem::class);
    }
}
