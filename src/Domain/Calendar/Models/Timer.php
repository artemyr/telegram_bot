<?php

namespace Domain\Calendar\Models;

use Domain\TelegramBot\Models\Notifications;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timer extends Model
{
    use SoftDeletes;
    use MassPrunable;

    protected $fillable = [
        'telegram_user_id',
        'class',
        'startDate',
        'code',
        'title',
    ];

    protected $casts = [
        'startDate' => 'datetime',
    ];

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth());
    }

    protected function getActiveAttribute(): bool
    {
        return (now()->getTimestamp() < $this->startDate->getTimestamp());
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_id', 'telegram_user_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notifications::class, 'notifiable');
    }
}
