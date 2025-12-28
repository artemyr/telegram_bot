<?php

namespace Services\HumanDateParser;

use Illuminate\Pipeline\Pipeline;
use Support\Contracts\HumanDateParserContract;

class Parser implements HumanDateParserContract
{
    protected string $type;
    protected array $rule;

    protected string $startString;
    protected bool $error = false;

    public function __construct()
    {
    }

    public function fromString(string $date): HumanDateParserContract
    {
        $this->startString = mb_strtolower($date);
        $this->parse();
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRule(): array
    {
        return $this->rule;
    }

    public function isError(): bool
    {
        return $this->error;
    }

    private function parse(): void
    {
        $result = app(Pipeline::class)
            ->send([
                'startString' => $this->startString
            ])
            ->through([
                new Month(),
                new Week()
            ])
            ->thenReturn();

        if (empty($result['type'])) {
            $this->error = true;
            return;
        }

        $this->type = $result['type'];
        $this->rule = $result['rule'];
    }
}
