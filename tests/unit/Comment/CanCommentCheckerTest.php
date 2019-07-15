<?php

namespace App\UnitTests\Comment;

use App\Comment\CanCommentChecker;
use App\Comment\FloodingChecker;
use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\ForbiddenException;
use App\UnitTests\UnitTestCase;

class CanCommentCheckerTest extends UnitTestCase
{
    /** @var FloodingChecker */
    private $floodingCheckerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->floodingCheckerMock = $this->getMockBuilder(FloodingChecker::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $this->floodingCheckerMock->method('check')
                                  ->willReturn(false);
    }

    public function testGivenACommentMadeByASubscriberShouldReturnTrueOnCheck()
    {
        $comment = new Comment();

        $author          = new Account();
        $author->role    = Account::ROLE_SUBSCRIBER;
        $comment->author = $author;

        $canCommentChecker = new CanCommentChecker($this->floodingCheckerMock);

        $this->assertTrue($canCommentChecker->check($comment));
    }

    public function testGivenACommentMadeByAGuestOnASubscriberPostShouldReturnTrueOnCheck()
    {
        $comment = new Comment();

        $comment->author = new Account();
        $comment->author->role = Account::ROLE_GUEST;
        $comment->post = new Post();
        $comment->post->author = new Account();
        $comment->post->author->role = Account::ROLE_SUBSCRIBER;

        $canCommentChecker = new CanCommentChecker($this->floodingCheckerMock);

        $this->assertTrue($canCommentChecker->check($comment));
    }

    public function testGivenACommentMadeByAGuestOnAGuestPostWithoutCoinShouldThrowExceptionOnCheck()
    {
        $comment = new Comment();

        $comment->author = new Account();
        $comment->author->role = Account::ROLE_GUEST;
        $comment->post = new Post();
        $comment->post->author = new Account();
        $comment->post->author->role = Account::ROLE_GUEST;

        $this->expectException(ForbiddenException::class);
        
        $canCommentChecker = new CanCommentChecker($this->floodingCheckerMock);
        $canCommentChecker->check($comment);
    }
}
