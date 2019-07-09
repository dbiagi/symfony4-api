<?php


namespace App\Comment;

use App\Entity\Account;
use App\Entity\Comment;
use App\Exception\FloodingException;
use App\Exception\ForbiddenException;

class CanCommentChecker
{
    /**
     * @var FloodingChecker
     */
    private $floodingChecker;

    public function __construct(FloodingChecker $floodingChecker)
    {
        $this->floodingChecker = $floodingChecker;
    }

    /**
     * Checa se o usuário pode comentar na postagem.
     *
     * @param Comment $comment
     * @return bool
     * @throws FloodingException se o usuário comentar mais vezes do que o permitido no intervalo de tempo
     * @throws ForbiddenException se o usuário não tiver permissão para comentar na postagem
     */
    public function check(Comment $comment): bool
    {
        $this->floodingChecker->check($comment->author);

        if ($comment->coins === 0 && $comment->author->role === Account::ROLE_GUEST && $comment->post->author === Account::ROLE_GUEST) {
            throw new ForbiddenException('You can\'t comment on this post');
        }

        return true;
    }
}