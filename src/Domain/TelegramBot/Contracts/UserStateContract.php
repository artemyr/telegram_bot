<?php

namespace Domain\TelegramBot\Contracts;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\Enum\LastMessageType;

interface UserStateContract
{
    public function get(): ?UserStateDto;
    public function write(UserStateDto $user): void;
    public function make(
        int $userId,
        BotState $state,
        bool $keyboard = false,
        LastMessageType $lastMessageType = LastMessageType::USER_MESSAGE,
    ): UserStateDto;
    public function changeState(BotState $state): void;
    public function changeLastMessageType(LastMessageType $type): void;
    public function changeKeyboard(bool $active): void;
    public static function fake(): void;
}
