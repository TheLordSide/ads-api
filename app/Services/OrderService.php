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
            'seller _id' => $ad->user_id,
            'price' => $ad->price,
            'status' => 'PENDING'
        ]);
    }


    public function confirm(Order $order)
    {

        //Confirmation de la commande

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
}
