<?php

namespace App\FunctionalTests\Comment;

use Psr\Container\ContainerInterface;
use App\Comment\FloodingChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FloddingCheckerTest extends KernelTestCase
{
    /**
     * @var FloodingChecker|object
     */
    private $service;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->service = $kernel->getContainer()->get(FloodingChecker::class);
    }

    public function testRepo()
    {
        $this->assertNotNull($this->service);
    }

}