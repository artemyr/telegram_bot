<?php

namespace Domain\TelegramBot\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramUser extends Model
{
    protected $fillable = [
        'timezone'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
