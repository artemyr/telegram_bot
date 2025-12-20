<?php

namespace Domain\TelegramBot\Dto;

use Domain\TelegramBot\BotState;

readonly class UserStateDto
{
    public function __construct(
        public int $userId,
        public int $chatId,
        public BotState $state,
        public string $timezone,
        public bool $keyboard,
    ) {
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'chatId' => $this->chatId,
            'state' => $this->state,
            'timezone' => $this->timezone,
            'keyboard' => $this->keyboard,
        ];
    }

    public static function fromArray(array $values): self
    {
        return new self(
            $values['userId'],
            $values['chatId'],
            $values['state'],
            $values['timezone'],
            $values['keyboard'],
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
                    $str .= json_encode($field, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
                    break;
            }
        }

        return $str;
    }
}
