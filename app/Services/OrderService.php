<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\User;
use App\Models\Order;


class OrderService
{

    public function create(User $buyer, Ad $ad)
    {

        // creer ensuite la commande
        return Order::create([
            'ad_id' => $ad->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $ad->user_id,
            'price' => $ad->price,
            'status' => 'PENDING'
        ]);
    }


    public function confirm(Order $order)
    {   
        //confirmation de la commande
        $order->update([
            'status' => 'CONFIRMED'
        ]);

        return $order;
    }


    public function cancel(Order $order)
    {
        //Annulation de la commande
        $order->update([
            'status' => 'CANCELLED'
        ]);

        return $order;
    }

    // Lister les commandes d'un utilisateur (acheteur ou vendeur)
    public function listForUser(User $user)
    {
        return Order::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->latest()
            ->paginate(10);
    }

}
