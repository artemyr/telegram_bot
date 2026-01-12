<?php

namespace Domain\Tasks\Models;

use App\Jobs\NotificationJob;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use SoftDeletes;
    use Prunable;

    protected $fillable = [
        'title',
        'telegram_user_id',
        'deadline',
        'priority',
        'repeat',
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
                dispatch(new NotificationJob(Task::class, $task->id))
                    ->delay($deadline);

                $warning = $deadline->subMinutes(10);
                if (($warning->getTimestamp() > now()->getTimestamp())) {
                    dispatch(new NotificationJob(Task::class, $task->id))
                        ->delay($warning);
                }

                $warning = $deadline->subHours(3);
                if (($warning->getTimestamp() > now()->getTimestamp())) {
                    dispatch(new NotificationJob(Task::class, $task->id))
                        ->delay($warning);
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
            get: fn($value) => mb_ucfirst($value),
            set: fn($value) => trim(mb_strtolower($value)),
        );
    }

    #[Scope]
    protected function sorted(Builder $query): void
    {
        $query->orderBy('priority', 'DESC');
        $query->orderBy('deadline', 'ASC');
    }

    #[Scope]
    protected function single(Builder $query): void
    {
        $query->where('repeat', false);
    }

    #[Scope]
    protected function repeat(Builder $query): void
    {
        $query->where('repeat', true);
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_id');
    }

    public function taskRecurrences(): HasMany
    {
        return $this->hasMany(TaskRecurrence::class);
    }
}
