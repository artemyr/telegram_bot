<?php

namespace Domain\TelegramBot\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifications extends Model
{
    protected $fillable = [
        'date',
        'pattern',
        'message',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    #[Scope]
    protected function arrived(Builder $query): void
    {
        $query->where('date', '<=', now());
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
