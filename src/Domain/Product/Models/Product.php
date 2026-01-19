<?php

namespace Domain\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'exist',
        'expire_days',
        'expire',
        'buy_at',
        'store',
    ];

    protected $casts = [
        'buy_at' => 'datetime',
        'expire' => 'datetime',
        'exist' => 'boolean',
    ];
}
