<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderAddress extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'addressable',
        'country',
        'street',
        'city',
        'state',
        'zip'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
