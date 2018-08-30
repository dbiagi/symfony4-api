<?php declare(strict_types=1);

namespace App\Helper;

use App\Entity\Comment;

class CommentSorter
{
    public static function sort(array $comments): array
    {
        $now = new \DateTime();

        usort($comments, function (Comment $a, Comment $b) use ($now) {
            $dateA = clone $a->createdAt;
            $dateB = clone $b->createdAt;

            if($a->coins > 0) {
                $dateA = $dateA->modify(sprintf('+%d minutes', $a->coins));
                $dateA = $dateA > $now ? $dateA : clone $a->createdAt;
            }

            if($b->coins > 0) {
                $dateB = $dateB->modify(sprintf('+%d minutes', $b->coins));
                $dateB = $dateB > $now ? $dateB : clone $b->createdAt;
            }

            if($dateA->getTimestamp() === $dateB->getTimestamp()) return 0;

            return $dateB > $dateA ? 1 : -1;
        });

        return $comments;
    }
}