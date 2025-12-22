<?php

namespace App\Models;

use Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskRecurrence extends Model
{
    protected $fillable = [
        'task_id',
        'type',
        'rule',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'rule' => 'json'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
