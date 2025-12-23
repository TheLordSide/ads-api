<?php

namespace App\Services;

use App\Models\Ad;

class SearchService
{
    public function search(array $filters)
    {
        return Ad::query()
            ->when(
                $filters['q'] ?? null,
                fn($q, $term) =>
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', "%$term%")
                        ->orWhere('description', 'like', "%$term%");
                })
            )
            ->when(
                $filters['category_id'] ?? null,
                fn($q, $cat) => $q->where('category_id', $cat)
            )
            ->when(
                $filters['min_price'] ?? null,
                fn($q, $min) => $q->where('price', '>=', $min)
            )
            ->when(
                $filters['max_price'] ?? null,
                fn($q, $max) => $q->where('price', '<=', $max)
            )
            ->when($filters['sort'] ?? null, function ($q, $sort) {
                match ($sort) {
                    'price_asc'  => $q->orderBy('price', 'asc'),
                    'price_desc' => $q->orderBy('price', 'desc'),
                    default      => $q->latest(),
                };
            })
            ->paginate(10);
    }
}
