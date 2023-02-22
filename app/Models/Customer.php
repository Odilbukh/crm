<?php

namespace App\Models;

use App\Enums\CustomerTypeEnum;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
        'tax_number',
        'bank_account',
        'type',
    ];

    protected $casts = [
        'birthday' => 'date',
        'gender' => GenderEnum::class,
        'type' => CustomerTypeEnum::class
    ];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Order::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
