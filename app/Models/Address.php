<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'addressable',
        'country',
        'street',
        'city',
        'state',
        'zip',
        'full_address',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

//    public function customers()
//    {
//        return $this->morphedByMany(Customer::class, 'addressable');
//    }
//
//    public function brands()
//    {
//        return $this->morphedByMany(Brand::class, 'addressable');
//    }
}
