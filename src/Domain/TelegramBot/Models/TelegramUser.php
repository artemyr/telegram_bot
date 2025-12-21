<?php

namespace Domain\TelegramBot\Models;

use App\Models\User;
use Domain\Calendar\Models\Timer;
use Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    protected $fillable = [
        'timezone'
    ];

    public function timezone(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? config('app.timezone')
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'telegram_user_id', 'telegram_id');
    }

    public function timers(): HasMany
    {
        return $this->hasMany(Timer::class, 'telegram_user_id', 'telegram_id');
    }
}
