<?php

namespace Support\Traits;

trait Runable
{
    public static function run(): static
    {
        $self = new static();
        $self->handle();
        return $self;
    }

    public function handle(): void
    {
    }
}
