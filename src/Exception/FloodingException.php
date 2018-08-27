<?php declare(strict_types=1);

namespace App\Exception;

class FloodingException extends \Exception
{
    private const MESSAGE = 'You can\'t comment now. Wait %d seconds to comment again.';

    public function __construct(int $cooldownRemaining)
    {
        parent::__construct(sprintf(self::MESSAGE, $cooldownRemaining));
    }
}