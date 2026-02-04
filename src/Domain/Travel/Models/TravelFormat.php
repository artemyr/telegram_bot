<?php

namespace Domain\Travel\Models;

use Domain\Travel\Factories\TravelFormatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TravelFormat extends Model
{
    use HasFactory;

    protected function travelClaim(): HasOne
    {
        return $this->hasOne(TravelClaim::class);
    }

    protected static function newFactory()
    {
        return TravelFormatFactory::new();
    }
}
