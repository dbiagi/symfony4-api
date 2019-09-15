<?php

namespace App\Event;

use App\Entity\Comment;

class CommentEventArgs
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}