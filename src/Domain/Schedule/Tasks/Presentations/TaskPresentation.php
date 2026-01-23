<?php

namespace Domain\Schedule\Tasks\Presentations;

use Domain\Schedule\Tasks\Models\Task;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Collection;

class TaskPresentation
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
        if (empty($this->timezone)) {
            $this->timezone = config('app.timezone');
        }

        $table = new TableDto();
        foreach ($this->tasks as $task) {

            /** @var Task $task */

            $now = now($this->timezone);
            $deadline = $task->deadline
                ?->setTimezone($this->timezone);

            $diff = $now->diffForHumans($deadline);

            $row = new RowDto();

            $row->addCol(new ColDto($task->id, 'id', true));
            $row->addCol(new ColDto($task->title, 'title'));
            $row->addCol(new ColDto(
                $deadline
                    ?->format('d.m.Y H:i'),
                'deadline'
            ));

            if ($deadline) {
                $row->addCol(new ColDto("($diff)", 'diff'));
            } else {
                $createdAt = $task->created_at;
                $diff = floor($createdAt->diffInDays($now));
                if ($diff > 0) {
                    $row->addCol(new ColDto("(создано $diff дней назад)", 'diff'));
                }
            }

            $table->addRow($row);
        }

        return $table;
    }
}
