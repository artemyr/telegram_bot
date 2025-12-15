<?php

namespace Domain\Calendar\Actions;

class StartWorkAction
{
    public function __invoke(): void
    {
        bot()->sendMessage('Вы начали рабочий день. Напомню вам когда его нужно будет завершить');
    }
}
