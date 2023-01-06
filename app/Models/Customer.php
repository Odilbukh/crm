<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'photo',
        'gender',
        'phone_1',
        'phone_2',
        'birthday',
        'in_blacklist',
        'notes',
        'company',
        'position'
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Order::class, 'customer_id');
    }
}
