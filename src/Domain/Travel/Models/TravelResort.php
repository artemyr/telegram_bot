<?php

namespace Domain\Travel\Models;

use Domain\Travel\Factories\TravelResortFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelResort extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TravelResortFactory::new();
    }
}
