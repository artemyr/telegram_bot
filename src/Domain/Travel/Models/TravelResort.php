<?php

namespace Domain\Travel\Models;

use Domain\Travel\Factories\TravelResortFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelResort extends Model
{
    use HasFactory;

    public function travelClaims(): HasMany
    {
        return $this->hasMany(TravelClaim::class);
    }

    protected static function newFactory()
    {
        return TravelResortFactory::new();
    }
}
