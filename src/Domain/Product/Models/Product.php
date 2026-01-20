<?php

namespace Domain\Product\Models;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'telegram_user_id',
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

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_id', 'telegram_user_id');
    }
}
