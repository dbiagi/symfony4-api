<?php

namespace App\Comment;

use App\Entity\Account;
use App\Entity\Comment;
use App\Exception\FloodingException;
use App\Helper\DateTimeHelper;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class FloodingChecker
{
    /** @var CommentRepository */
    private $commentsRepository;

    /** @var int */
    private $commentCooldown;

    public function __construct(EntityManagerInterface $em, $commentCooldown)
    {
        $this->commentsRepository = $em->getRepository(Comment::class);
        $this->commentCooldown    = $commentCooldown;
    }

    /**
     * @param Account $account
     * @return bool
     *
     * @throws FloodingException se $account estiver enviando mais mensagens do que o permitido no intervalo de tempo.
     */
    public function check(Account $account): bool
    {
        $comment = $this->commentsRepository->findLastCommentByAccountId($account->uuid);

        if ($comment === null) {
            return false;
        }

        $now = new DateTime();

        $secondsAfterLastComment = DateTimeHelper::getDiffInSeconds($comment->createdAt, $now);

        if ($secondsAfterLastComment < $this->commentCooldown) {
            throw new FloodingException($this->commentCooldown - $secondsAfterLastComment);
        }

        return false;
    }
}