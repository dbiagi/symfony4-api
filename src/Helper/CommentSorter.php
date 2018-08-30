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
            if($a->coins === $b->coins) return 0;
            else return $a->coins < $b->coins ? -1 : 1;
        });

        usort($normal, function(Comment $a, Comment $b){
            if($a->createdAt->getTimestamp() === $b->createdAt->getTimestamp()) return 0;
            else return $a->createdAt < $b->createdAt ? -1 : 1;
        });

        return array_merge($featureds, $normal);
    }
}
