<?php

namespace Domain\TelegramBot\Dto\Table;

class RowDto
{
    public function __construct(
        /** @param ColDto[] $cols */
        public array $cols = []
    )
    {
    }

    public function addRow(ColDto $col): self
    {
        $this->cols[] = $col;
        return $this;
    }

    public function __toString(): string
    {
        $res = [];

        foreach ($this->cols as $col) {
            $res[] = (string)$col;
        }

        return implode(' ', $res);
    }

    public function getCol(string $code): ?ColDto
    {
        foreach ($this->cols as $col) {
            if ($col->code === $code) {
                return $col;
            }
        }

        return null;
    }
}
