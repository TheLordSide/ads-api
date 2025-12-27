<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    //policies d'acces aux commandes
    public function create(User $user, Ad $ad)
    {
        // l'auteur de l'annonce NE PEUT PAS commander
        return $user->id !== $ad->user_id;
    }

    public function confirm(User $user, Order $order)
    {
        //seul le vendeur confirme la commande
        return $order->seller_id === $user->id
            && $order->status === 'PENDING';
    }

    public function cancel(User $user, Order $order)
    {
        // l'acheteur ou le buyer peut annuler la commande
        return ($user->id === $order->seller_id || $user->id === $order->buyer_id)
            && $order->status === 'PENDING';
    }
}
