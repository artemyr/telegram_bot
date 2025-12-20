<?php

namespace Domain\Tasks\Models;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'telegram_user_id',
        'deadline',
        'priority',
    ];

    protected $casts = [
        'deadline' => 'datetime'
    ];

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn ($value) => trim(mb_strtolower($value)),
        );
    }

    #[Scope]
    protected function sorted(Builder $query): void
    {
        $query->orderBy('priority', 'DESC');
        $query->orderBy('deadline', 'ASC');
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_id', 'telegram_user_id');
    }
}
