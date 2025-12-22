<?php

namespace Support\Dto;

use Illuminate\Database\Eloquent\Model;

readonly class RepositoryResult
{
    public const SUCCESS_SAVED = 0;
    public const ERROR = 1;
    public const EXISTS = 2;
    public const RESTORED = 3;
    public const SUCCESS_DELETED = 4;


    public function __construct(
        public int $state,
        public ?Model $model = null,
        public ?string $message = null,
    ) {}

    public static function error(string $message): self
    {
        return new self(self::ERROR, null, $message);
    }
}
