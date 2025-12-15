<?php

namespace Services\Telegram;

use Illuminate\Support\Facades\Http;
use Services\Telegram\Exceptions\TelegramBotApiException;
use Throwable;

class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';
    protected static bool $silent = false;

    public static function fake(): TelegramBotApiFake
    {
        return app()->instance(
            TelegramBotApiContract::class,
            new TelegramBotApiFake()
        );
    }

    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        try {
            if (self::$silent) {
                return false;
            }

            $response = Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
            ])->throw()->json();

            $ok = $response['ok'] ?? false;

            return (bool) tap($ok, function ($ok) use ($text) {
                if (!$ok) {
                    self::$silent = true;
                    logger()->alert("Telegram didn't send message. text: " . $text);
                }
            });
        } catch (Throwable $e) {
            self::$silent = true;
            report(new TelegramBotApiException($e->getMessage()));

            return false;
        }
    }
}
