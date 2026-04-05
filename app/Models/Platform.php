<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function variables(): HasMany
    {
        return $this->hasMany(PlatformVariable::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPlatformPrice::class);
    }
}
