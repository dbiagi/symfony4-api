<?php

namespace App\UnitTests\Paginator;

use App\Paginator\Paginator;
use App\UnitTests\UnitTestCase;
use RuntimeException;

class PaginatorTest extends UnitTestCase
{
    public function testGivenAnArrayShouldPaginate()
    {
        $collection = range('a', 'z');

        $itensPerPage = 10;

        $paginator = new Paginator();
        $pagination = $paginator->paginate($collection, 1, $itensPerPage);

        $this->assertSame(count($collection), $pagination->getCount());
        $this->assertSame($itensPerPage, count($pagination->getData()));
    }

    public function testGivenUnsopportedCollectionShouldThrowException() {
        $this->expectException(RuntimeException::class);

        $paginator = new Paginator();

        $paginator->paginate(new \stdClass());
    }
}