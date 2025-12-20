<?php

namespace Domain\Tasks\Models;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'telegram_user_id',
        'deadline',
        'priority',
    ];

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn ($value) => mb_strtolower($value),
        );
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_id', 'telegram_user_id');
    }
}
