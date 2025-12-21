<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifications extends Model
{
    protected $fillable = [
        'date',
        'pattern',
        'message',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
