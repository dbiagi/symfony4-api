<?php declare(strict_types=1);

namespace App\Exception;

class NotEnoughCoinsException extends \Exception
{
    private const MESSAGE = 'Você não tem saldo suficiente =(. Está faltando somente %d moedas.';

    public function __construct(int $coinsLeft)
    {
        parent::__construct(sprintf(self::MESSAGE, $coinsLeft));
    }
}