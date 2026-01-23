<?php

namespace Support\Traits;

trait Createable
{
    public static function create()
    {
        $self = new static();
        return $self->handle();
    }

    public function handle(): mixed
    {
        return null;
    }
}
