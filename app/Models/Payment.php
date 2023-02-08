<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'reference',
        'provider',
        'method',
        'amount',
        'currency'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
