<?php

namespace Domain\TelegramBot\Dto\Table;

class TableDto
{
    public function __construct(
        /** @param RowDto[] $rows */
        public array $rows = []
    ) {
    }

    public function addRow(RowDto $row): self
    {
        $this->rows[] = $row;
        return $this;
    }

    public function __toString(): string
    {
        if ($this->empty()) {
            return 'Пусто...';
        }

        $res = [];
        $num = 1;

        foreach ($this->rows as $row) {
            $res[] = "$num) $row";
            $num++;
        }

        return implode("\n", $res);
    }

    public function empty(): bool
    {
        return empty($this->rows);
    }

    public function getRow(int $rowId): ?RowDto
    {
        $i = 1;

        foreach ($this->rows as $row) {
            if ($rowId === $i) {
                return $row;
            }
            $i++;
        }

        return null;
    }
}
