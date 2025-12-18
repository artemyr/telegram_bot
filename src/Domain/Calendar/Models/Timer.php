<?php

namespace Domain\Calendar\Models;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'telegram_user_id',
        'class',
        'startDate',
        'code',
        'title',
    ];
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_id', 'telegram_user_id');
    }
}
