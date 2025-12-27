<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\OrderResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Liste des commandes
    public function index()
    {
        try {
            $orders = $this->orderService->listForUser(request()->user());
            
            if ($orders->isEmpty()) {
                return ErrorResource::throwError('No orders found for this user', 404);
            }

            return OrderResource::collection($orders);
        } catch (\Exception) {
            return ErrorResource::throwError('Error fetching orders', 500);
        }
    }

    // Création de la commande
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'ad_id' => 'required|integer|exists:ads,id',
            ]);

            $ad = Ad::find($data['ad_id']);

            // Vérification : l'utilisateur ne peut pas commander sa propre annonce
            if ($request->user()->id === $ad->user_id) {
                return ErrorResource::throwError(
                    'You cannot order your own ad',
                    403
                );
            }

            $order = $this->orderService->create($request->user(), $ad);

            return new OrderResource($order);
            
        } catch (\Illuminate\Validation\ValidationException) {
            return ErrorResource::throwError('Ad id not found', 422);
        } catch (\Exception ) {
            return ErrorResource::throwError('Error creating order', 500);
        }
    }

    // Confirmation de la commande
    public function confirm(int $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Vérifier que l'utilisateur est bien le vendeur
            if (request()->user()->id !== $order->seller_id) {
                return ErrorResource::throwError(
                    'Unauthorized | Only the seller can confirm this order',
                    403
                );
            }

            // Vérifier que la commande est en statut PENDING
            if ($order->status !== 'PENDING') {
                return ErrorResource::throwError(
                    'Order cannot be confirmed. Current status: ' . $order->status,
                    422
                );
            }

            $order = $this->orderService->confirm($order);

            return new OrderResource($order);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ErrorResource::throwError('Order not found', 404);
        } catch (\Exception ) {
            return ErrorResource::throwError('Error confirming order', 500);
        }
    }

    // Annulation de la commande
    public function cancel(int $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Vérifier que l'utilisateur est l'acheteur OU le vendeur
            if (request()->user()->id !== $order->buyer_id && request()->user()->id !== $order->seller_id) {
                return ErrorResource::throwError(
                    'Unauthorized | You cannot cancel this order',
                    403
                );
            }

            // Vérifier que la commande est en statut PENDING
            if ($order->status !== 'PENDING') {
                return ErrorResource::throwError(
                    'Order cannot be cancelled. Current status: ' . $order->status,
                    422
                );
            }

            $order = $this->orderService->cancel($order);

            return new OrderResource($order);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ErrorResource::throwError('Order not found', 404);
        } catch (\Exception) {
            return ErrorResource::throwError('Error cancelling order', 500);
        }
    }
}