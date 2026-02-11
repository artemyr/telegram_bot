<?php

namespace Domain\TelegramBot\Dto;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\LastMessageType;

readonly class UserStateDto
{
    public function __construct(
        public int $userId,
        public BotState $state,
        public bool $keyboard,
        public LastMessageType $lastMessageType,
    ) {
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'state' => $this->state,
            'keyboard' => $this->keyboard,
            'lastMessageType' => $this->lastMessageType,
        ];
    }

    public static function fromArray(array $values): self
    {
        return new self(
            $values['userId'],
            $values['state'],
            $values['keyboard'],
            $values['lastMessageType'],
        );
    }

    public function __toString(): string
    {
        $fields = $this->toArray();
        return $this->print($fields);
    }

    public function print(array $fields): string
    {
        $str = '';

        foreach ($fields as $name => $field) {
            switch (gettype($field)) {
                case 'boolean':
                    $str .= "$name: " . (($field) ? 'true' : 'false') . "\n";
                    break;
                case 'integer':
                case 'string':
                    $str .= "$name: \"$field\"\n";
                    break;
                case 'array':
                    if (empty($field)) {
                        $str .= $name . ": []" . "\n";
                        break;
                    }
                    $str .= $this->print($field) . "\n";
                    break;
                case 'object':
                    $str .= get_class($field) . ":\n";
                    $str .= json_encode($field, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                    break;
            }
        }

        return $str;
    }
}
