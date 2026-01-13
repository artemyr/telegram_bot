<?php

namespace Domain\TelegramBot\Services;

use App\Jobs\SendMessageJob;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Enum\LastMessageType;
use Domain\TelegramBot\Exceptions\MessageManagerException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use Throwable;

class MessageManager implements MessageContract
{
    protected string $driver;
    protected static bool $fake = false;
    protected array $log = [];

    protected string $text;
    protected InlineKeyboardMarkup|ReplyKeyboardMarkup|null $keyboard = null;
    protected bool $replyKeyboard = false;
    protected int $userId;
    protected int $delay = 0;

    public function __construct()
    {
        $this->driver = config('telegram_bot.messages.driver', 'runtime');

        $userId = bot()->userId();
        if (!empty($userId)) {
            $this->userId = $userId;
        }
    }

    public function text(string|array $text): MessageContract
    {
        if (is_array($text)) {
            $text = implode("\n", $text);
        }

        $this->text = $text;
        return $this;
    }

    public function delay(int $delay): MessageContract
    {
        $this->delay = $delay;
        return $this;
    }

    public function replyKeyboard(array $keyboard): MessageContract
    {
        $this->keyboard = ReplyKeyboardMarkup::make();
        foreach ($keyboard as $button) {
            $this->keyboard->addRow(
                KeyboardButton::make($button)
            );
        }
        $this->replyKeyboard = true;
        return $this;
    }

    public function inlineKeyboard(array $keyboard): MessageContract
    {
        $this->keyboard = InlineKeyboardMarkup::make();
        foreach ($keyboard as $data => $button) {
            $this->keyboard->addRow(
                InlineKeyboardButton::make(
                    $button,
                    callback_data: $data,
                )
            );
        }

        return $this;
    }

    public function userId(int $userId): MessageContract
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @throws MessageManagerException
     */
    public function send(): void
    {
        if (self::$fake) {
            $this->log[] = $this->text;
            return;
        }

        if (empty($this->userId)) {
            throw new MessageManagerException('Provide user id for send message');
        }

        $userDto = tuser();

        if (!empty($userDto) && $userDto->lastMessageType === LastMessageType::INLINE_KEYBOARD_BOT_MESSAGE) {
            try {
                $this->editLastMessage();
            } catch (Throwable $e) {
                report($e);
                $this->sendMessage();
            }
        } else {
            $this->sendMessage();
        }

        if ($this->replyKeyboard) {
            tuserstate()->changeKeyboard(true);
        }

        if ($this->keyboard instanceof InlineKeyboardMarkup) {
            tuserstate()->changeLastMessageType(LastMessageType::INLINE_KEYBOARD_BOT_MESSAGE);
        } elseif ($this->keyboard instanceof ReplyKeyboardMarkup) {
            tuserstate()->changeLastMessageType(LastMessageType::REPLY_KEYBOARD_BOT_MESSAGE);
        } else {
            tuserstate()->changeLastMessageType(LastMessageType::TEXT_BOT_MESSAGE);
        }

        $this->flush();
    }

    public static function fake(): void
    {
        self::$fake = true;
    }

    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * @throws MessageManagerException
     */
    private function editLastMessage(): void
    {
        if ($this->driver === 'jobs') {
            throw MessageManagerException::driverNotSupported();
        }

        if ($this->driver === 'runtime') {
            bot()->editMessageText(
                text: $this->text,
                chat_id: $this->userId,
                reply_markup: $this->keyboard,
            );
            return;
        }

        throw MessageManagerException::driverNotSupported();
    }

    /**
     * @throws MessageManagerException
     */
    private function sendMessage(): void
    {
        if ($this->driver === 'jobs') {
            dispatch(
                new SendMessageJob(
                    $this->userId,
                    $this->text,
                    $this->keyboard
                )
            );
            return;
        }

        if ($this->driver === 'runtime') {
            bot()->sendMessage(
                text: $this->text,
                chat_id: $this->userId,
                reply_markup: $this->keyboard,
            );
            return;
        }

        throw MessageManagerException::driverNotSupported();
    }

    private function flush(): void
    {
        $this->text = '';
        $this->keyboard = null;
        $this->replyKeyboard = false;
        $this->delay = 0;
    }
}
