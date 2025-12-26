<?php

namespace App\Services;

use App\Models\Ad;

class SearchService
{
    public function search(array $filters)
    {
        $query = Ad::query();

        // Recherche par titre / description
        $query->when($filters['q'] ?? null, function ($q, $term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });

        // filtre par categoroe
        $query->when($filters['category_id'] ?? null, function ($q, $cat) {
            $q->where('category_id', $cat);
        });

        // filtre par price range (min-max)
        $query->when($filters['min_price'] ?? null, function ($q, $min) {
            $q->where('price', '>=', $min);
        });

        $query->when($filters['max_price'] ?? null, function ($q, $max) {
            $q->where('price', '<=', $max);
        });

        // Option de tri par prix 
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        return $query->paginate(10);
    }
}
