<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'taxes';

    protected $fillable = [
        'name',
        'rate',
        'type',
        'enabled',
    ];

    protected $casts = [
        'rate' => 'double',
        'enabled' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_tax', 'tax_id', 'order_id');
    }
}
