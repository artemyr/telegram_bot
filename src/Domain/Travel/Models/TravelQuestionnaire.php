<?php

namespace Domain\Travel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelQuestionnaire extends Model
{
    protected $fillable = [
        'telegram_user_id',
    ];

    public function travelClaims(): HasMany
    {
        return $this->hasMany(TravelClaim::class);
    }

    public function travelStyles(): BelongsToMany
    {
        return $this->belongsToMany(TravelStyle::class);
    }
}
