<?php

namespace Domain\Schedule\Calendar\Presentations;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Collection;

class TimerPresentation
{
    public function __construct(
        protected Collection $timers,
        protected ?string    $timezone = null
    )
    {
    }

    public function __toString(): string
    {
        return (string)$this->getTable();
    }

    public function getTable(): TableDto
    {
        if (empty($this->timezone)) {
            $this->timezone = config('app.timezone');
        }

        $table = new TableDto();
        foreach ($this->timers as $timer) {

            /** @var Timer $timer */

            $now = now($this->timezone);
            $startDate = $timer->startDate
                ?->setTimezone($this->timezone);

            $diff = $now->diffForHumans($startDate);

            $row = new RowDto();

            $row->addCol(new ColDto($timer->id, 'id', true));
            $row->addCol(new ColDto($timer->title, 'title'));
            $row->addCol(new ColDto(
                $startDate
                    ?->format('d.m.Y H:i'),
                'startDate'
            ));

            if ($startDate) {
                $row->addCol(new ColDto("($diff)", 'diff'));
            }

            $table->addRow($row);
        }

        return $table;
    }
}
