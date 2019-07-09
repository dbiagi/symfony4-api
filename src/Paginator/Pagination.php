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
     * @param array $data
     *
     * @return Pagination
     */
    public function setData(array $data): Pagination
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return Pagination
     */
    public function setCount(int $count): Pagination
    {
        $this->count = $count;

        return $this;
    }

}