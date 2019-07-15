<?php declare(strict_types=1);

namespace App\Paginator;

class Pagination
{
    /** @var array */
    private $data;

    /** @var int */
    private $count;

    public function __construct(array $data, int $count)
    {
        $this->data  = $data;
        $this->count = $count;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}