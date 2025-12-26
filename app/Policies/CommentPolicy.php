<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{   

    //policies d'acces aux commentaires
    public function delete(User $user, Comment $comment)
    {   
        //l'auteur du commentaire peut supprimer le commentaire
        return $comment->user_id === $user->id;
    }
}

