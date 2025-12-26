<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;

class AdPolicy
{
    // Policies pour la gestion d'acces a l'annonce
    public function update(User $user, Ad $ad): bool
    {   

        //seul l'auteur de l'annonce peut update ladite annonce
        return $ad->user_id === $user->id;
    }

    public function delete(User $user, Ad $ad): bool
    {   
        //seul l'auteur de l'annonce peut delete son annonce
        return $ad->user_id === $user->id;
    }
}
