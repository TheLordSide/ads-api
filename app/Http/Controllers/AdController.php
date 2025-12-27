<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Services\AdService;
use Illuminate\Http\Request;
use App\Http\Resources\AdResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdController extends Controller
{
    use AuthorizesRequests;
    protected AdService $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    public function index(Request $request)
    {
        return AdResource::collection($this->adService->list($request->all()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        return new AdResource($this->adService->create($validated, $request->user()));
    }


    //affiche une annonce
    public function show(int $id)
    {
        try {
            $ad = Ad::find($id);
            if (!$ad) {
                return ErrorResource::throwError("Ad not found", 404);
            }

            return new AdResource($ad);
        } catch (\Exception) {
            return ErrorResource::throwError('Error fetching ad', 500);
        }
    }


    public function update(Request $request, int $id)
    {
        try {
            // Récupérer l'annonce manuellement
            $ad = Ad::find($id);
            if (!$ad) {
                return ErrorResource::throwError("Ad not found ", 404);
            }

            // Vérifier l'autorisation
            $this->authorize('update', $ad);

            // Validation des données
            $data = $request->validate([
                'title' => 'sometimes|string',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric',
                'category_id' => 'sometimes|exists:categories,id'
            ]);

            // Mise à jour via le service
            $ad = $this->adService->update($ad, $data);

            return new AdResource($ad);
        } catch (\Illuminate\Validation\ValidationException) {
            return ErrorResource::throwError('Validation failed', 422);
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return ErrorResource::throwError('Unauthorized | you cannot update this ad', 403);
        } catch (\Exception) {
            return ErrorResource::throwError('Error updating ad', 500);
        }
    }



    public function destroy(int $id)
    {
        try {
            // Récupérer l'annonce manuellement
            $ad = Ad::find($id);
            if (!$ad) {
                return ErrorResource::throwError("Ad not found", 404);
            }

            // Vérifier l'autorisation
            $this->authorize('delete', $ad);

            // Suppression via le service
            $this->adService->delete($ad);

            return response()->noContent();
        } catch (AuthorizationException ) {
            return ErrorResource::throwError('Unauthorized | You cannot delete this ad', 403);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error deleting ad', 500);
        }
    }
}
