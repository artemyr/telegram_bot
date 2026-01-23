<?php

namespace Domain\Schedule\Tasks\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskRecurrence extends Model
{
    protected $fillable = [
        'task_id',
        'type',
        'days_of_week',
        'days_of_month',
        'time',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'days_of_week' => 'json',
        'days_of_month' => 'json',
        'time' => 'datetime',
    ];

    public function time(): Attribute
    {
        /** @var Carbon $value */
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)
                ->format('H:i'),
        );
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
