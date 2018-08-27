<?php declare(strict_types=1);

namespace App\Exception;

class NotEnoughCoinsException extends \Exception
{
    private const MESSAGE = 'You don\'t have enough coins. Buy more %d to post this comment.';

    public function __construct(int $coinsLeft)
    {
        parent::__construct(sprintf(self::MESSAGE, $coinsLeft));
    }
}