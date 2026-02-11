<?php

namespace Domain\Schedule\Tasks\Presentations;

use Domain\Schedule\Tasks\Models\TaskRecurrence;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Collection;

class RecurrenceTaskPresentation
{
    public function __construct(
        protected Collection $tasks,
        protected ?string $timezone = null
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->getTable();
    }

    public function getTable(): TableDto
    {
        $table = new TableDto();
        foreach ($this->tasks as $task) {

            /** @var TaskRecurrence $rtask */
            $row = new RowDto();

            $row->addCol(new ColDto($task->id, 'id', true));
            $row->addCol(new ColDto($task->title, 'title'));

            $table->addRow($row);
        }

        return $table;
    }
}
