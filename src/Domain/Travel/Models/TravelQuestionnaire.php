<?php

namespace Domain\Travel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelQuestionnaire extends Model
{
    protected $fillable = [
        'telegram_user_id',
        'name',
    ];

    public function travelClaims(): HasMany
    {
        return $this->hasMany(TravelClaim::class);
    }
}
