<?php

namespace App\UnitTests\Comment;

use App\Comment\FloodingChecker;
use App\Entity\Account;
use App\Entity\Comment;
use App\Exception\FloodingException;
use App\Repository\CommentRepository;
use App\UnitTests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FloddingCheckerTest extends UnitTestCase
{
    /** @var int */
    const COMMENT_COOLDOWN = 10;

    /** @var MockObject */
    private $commentRepository;

    public function testGivenCommentAfterCooldownShouldReturnFalseOnCheck()
    {
        $account = $this->getAccountAbleToComment();

        $this->commentRepository->method('findLastCommentByAccountId')
                                ->with($account->uuid)
                                ->willReturn($this->getLastCommentAfterCooldown($account));

        $floodingChecker = new FloodingChecker($this->entityManagerMock, self::COMMENT_COOLDOWN);

        $this->assertFalse($floodingChecker->check($account));
    }

    public function testGivenCommentOnCooldownShouldThrowException()
    {
        $account = $this->getAccountAbleToComment();

        $this->commentRepository->method('findLastCommentByAccountId')
                                ->with($account->uuid)
                                ->willReturn($this->getLastCommentOnCooldown($account));

        $this->expectException(FloodingException::class);

        $floodingChecker = new FloodingChecker($this->entityManagerMock, self::COMMENT_COOLDOWN);
        $floodingChecker->check($account);
    }

    public function testGivenAnAccountWithoutCommentsShouldReturnFalseOnCheck()
    {
        $account = $this->getAccountAbleToComment();

        $this->commentRepository->method('findLastCommentByAccountId')
                                ->with($account->uuid)
                                ->willReturn(null);

        $floodingChecker = new FloodingChecker($this->entityManagerMock, self::COMMENT_COOLDOWN);
        $this->assertFalse($floodingChecker->check($account));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commentRepository = $this->getMockBuilder(CommentRepository::class)
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $this->entityManagerMock->method('getRepository')
                                ->with(Comment::class)
                                ->willReturn($this->commentRepository);
    }

    private function getAccountAbleToComment(): Account
    {
        $account = new Account();

        $account->uuid  = 1;
        $account->name  = $this->faker->name;
        $account->role  = Account::ROLE_SUBSCRIBER;
        $account->email = $this->faker->email;

        return $account;
    }

    private function getLastCommentAfterCooldown(Account $account): Comment
    {
        $comment = new Comment();

        $comment->author    = $account;
        $comment->createdAt = new \DateTime();
        $comment->createdAt->sub(\DateInterval::createFromDateString(sprintf('+%d seconds', self::COMMENT_COOLDOWN + 1)));

        return $comment;
    }

    private function getLastCommentOnCooldown(Account $account): Comment
    {
        $comment = new Comment();

        $comment->author    = $account;
        $comment->createdAt = new \DateTime();

        return $comment;
    }
}