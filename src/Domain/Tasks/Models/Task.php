<?php

namespace Domain\Tasks\Models;

use Domain\TelegramBot\Models\Notifications;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use SoftDeletes;
    use MassPrunable;

    protected $fillable = [
        'title',
        'telegram_user_id',
        'deadline',
        'priority',
    ];

    protected $casts = [
        'deadline' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Task $task) {

            /** @var Carbon $deadline */
            $deadline = $task->deadline;

            if (!empty($deadline) && ($deadline->getTimestamp() > now()->getTimestamp())) {
                $task->notifications()
                    ->create([
                        'date' => $deadline,
                    ]);
                $warning = $deadline->subMinutes(10);
                if (($warning->getTimestamp() > now()->getTimestamp())) {
                    $task->notifications()
                        ->create([
                            'date' => $warning,
                        ]);
                }
                $warning = $deadline->subHours(3);
                if (($warning->getTimestamp() > now()->getTimestamp())) {
                    $task->notifications()
                        ->create([
                            'date' => $warning,
                        ]);
                }
            }
        });
    }

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth());
    }

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => trim(mb_strtolower($value)),
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
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notifications::class, 'notifiable');
    }
}
