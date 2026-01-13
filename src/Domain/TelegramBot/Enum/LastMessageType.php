<?php

namespace Domain\TelegramBot\Enum;

enum LastMessageType
{
    case INLINE_KEYBOARD_BOT_MESSAGE;
    case REPLY_KEYBOARD_BOT_MESSAGE;
    case TEXT_BOT_MESSAGE;
    case USER_MESSAGE;
}
