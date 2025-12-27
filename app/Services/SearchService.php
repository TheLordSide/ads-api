<?php

namespace App\Services;

use App\Models\Ad;

class SearchService
{
    public function search(array $filters)
    {
        $query = Ad::query();

        // Recherche par titre / description
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['q']}%")
                    ->orWhere('description', 'like', "%{$filters['q']}%");
            });
        }

        // Filtre par catégorie
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filtre par price range
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Tri (par défaut latest)
        $sort = $filters['sort'] ?? 'latest';

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'oldest':
                $query->oldest();
                break;

            case 'latest':
            default:
                $query->latest();
                break;
        }


        return $query->paginate(10);
    }
}
