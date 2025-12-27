<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Http\Resources\AdResource;
use App\Http\Resources\ErrorResource;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Recherche intelligente des annonces
     */
    public function index(Request $request)
    {
        try {
            // Validation des filtres
            $filters = $request->validate([
                'q'           => 'nullable|string|max:255',
                'category_id' => 'nullable|integer|exists:categories,id',
                'min_price'   => 'nullable|numeric|min:0',
                'max_price'   => 'nullable|numeric|min:0',
                'sort' => 'nullable|in:price_asc,price_desc,oldest,latest',

            ]);

            $ads = $this->searchService->search($filters);

            // Vérifier si aucune annonce n'a été trouvée
            if ($ads->isEmpty()) {
                return ErrorResource::throwError('No ads found matching your criteria', 404);
            }

            return AdResource::collection($ads);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ErrorResource::throwError('Invalid filters', 422);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error searching ads', 500);
        }
    }
}
