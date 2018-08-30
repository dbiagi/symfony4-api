<?php declare(strict_types=1);

namespace App\Helper;

use App\Entity\Comment;

class CommentSorter
{
    public static function sort(array $comments): array
    {
        $featureds = [];
        $normal = [];

        $now = new \DateTime();

        foreach ($comments as $comment) {
            if($comment->coins > 0) {
                $createAt = clone $comment->createdAt;
                $createdAt->modify(sprintf('+%d minutes', $comment->coins));

                if($createAt > $now) {
                    $featureds[] = $comment;
                    continue;
                }
            }

            $normal[] = $comment;
        }

        usort($featureds, function (Comment $a, Comment $b) use ($now) {
            if($a->createdAt->getTimestamp())
        });

        return $comments;
    }
}
