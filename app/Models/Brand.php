<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model
{
    use HasFactory;
//    use InteractsWithMedia;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'website',
        'description',
        'position',
        'is_visible',
        'seo_title',
        'seo_description',
        'sort',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable', 'addressables');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
