<?php

namespace Support\Contracts;

interface HumanDateParserContract
{
    public function fromString(string $date): HumanDateParserContract;

    public function getType(): string;

    public function getRule(): array;
    public function isError(): bool;
}
