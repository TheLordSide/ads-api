<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\User;
use App\Models\Comment;

class CommentService{

    // liste des commentaires d'une annonce
    public function listByAd(Ad $ad){

        return $ad->comment()->latest()->get();
    }

    //ajout d'un commentaire
    public function create (Ad $ad, User $user, string $content){

        return $ad->comment()->create([
            'user_id' => $user->id,
            'content' => $content
        ]);
    }

    //suppression d'un commentaire
    public function delete(Comment $comment){
        $comment->delete();
    }
}