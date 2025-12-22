<?php

namespace Domain\Tasks\Presentations;

use App\Models\TaskRecurrence;
use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Collection;

class RecurrenceTaskPresentation
{
    public function __construct(
        protected Collection $tasks,
        protected ?string $timezone = null
    )
    {
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
            /** @var Task $rtask */

            $rtask = $task->taskRecurrence;

            $row = new RowDto();

            $row->addCol(new ColDto($task->id, 'id', true));
            $row->addCol(new ColDto($rtask->id, 'rid', true));
            $row->addCol(new ColDto($task->title, 'title'));

            $table->addRow($row);
        }

        return $table;
    }
}
