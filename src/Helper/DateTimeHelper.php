<?php declare(strict_types=1);

namespace App\Helper;

class DateTimeHelper
{
    /**
     * Calcula a diferença (b - a), em segundos.
     *
     * @param \DateTime $a
     * @param \DateTime $b
     *
     * @return float|int
     */
    public static function getDiffInSeconds(\DateTime $a, \DateTime $b)
    {
        $interval = $b->diff($a);

        return ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }
}