<?php

namespace Domain\Travel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelClaim extends Model
{
    protected $fillable = [
        'telegram_user_id',
    ];

    public function travelFormat(): BelongsTo
    {
        return $this->belongsTo(TravelFormat::class);
    }

    public function travelResort(): BelongsTo
    {
        return $this->belongsTo(TravelResort::class);
    }

    public function travelQuestionnaire(): BelongsTo
    {
        return $this->belongsTo(TravelQuestionnaire::class);
    }
}
