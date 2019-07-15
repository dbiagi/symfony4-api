<?php

namespace App\UnitTests;

use Doctrine\ORM\EntityManagerInterface;
use Faker\DefaultGenerator;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    /** @var Generator */
    protected $faker;

    /** @var MockObject */
    protected $entityManagerMock;

    protected function setUp(): void
    {
        $this->faker = new DefaultGenerator();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
                         ->disableOriginalConstructor()
                         ->getMock();
    }
}