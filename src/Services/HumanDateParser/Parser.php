<?php

namespace Services\HumanDateParser;

use Domain\Schedule\Tasks\Enum\TaskRepeatTypesEnum;
use Illuminate\Pipeline\Pipeline;
use Services\HumanDateParser\Collection\RecurrenceCollection;
use Services\HumanDateParser\Exceptions\ParserError;
use Services\HumanDateParser\Parsers\Day;
use Services\HumanDateParser\Parsers\Month;
use Services\HumanDateParser\Parsers\Week;
use Support\Contracts\HumanDateParserContract;

class Parser implements HumanDateParserContract
{
    const DOUBLE_SET_TYPE_ERROR = 1;
    const UNKNOWN_FORMAT_ERROR = 2;

    protected TaskRepeatTypesEnum $type;
    protected string $tz;
    protected RecurrenceCollection $recurrenceCollection;

    protected string $startString;
    protected int $errorCode;

    public function __construct()
    {
        $this->recurrenceCollection = RecurrenceCollection::make();
    }

    /**
     * @throws ParserError
     */
    public function fromString(string $date, ?string $tz = null): HumanDateParserContract
    {
        if (empty($tz)) {
            $this->tz = config('app.timezone');
        } else {
            $this->tz = $tz;
        }

        $this->startString = mb_strtolower($date);
        $this->parse();
        return $this;
    }

    public function getTimezone(): string
    {
        return $this->tz;
    }

    public function getStartString(): string
    {
        return $this->startString;
    }

    public function isError(): bool
    {
        return !empty($this->errorCode);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @throws ParserError
     */
    private function parse(): void
    {
        app(Pipeline::class)
            ->send($this)
            ->through([
                new Month(),
                new Week(),
                new Day(),
            ])
            ->thenReturn();

        if ($this->getCollection()->isEmpty()) {
            $this->errorCode = self::UNKNOWN_FORMAT_ERROR;
        }
    }

    public function getType(): TaskRepeatTypesEnum
    {
        return $this->type;
    }

    public function setType(TaskRepeatTypesEnum $value): self
    {
        if (!empty($this->type)) {
            $this->errorCode = self::DOUBLE_SET_TYPE_ERROR;
        }

        $this->type = $value;
        return $this;
    }

    public function getCollection(): RecurrenceCollection
    {
        return $this->recurrenceCollection;
    }
}
