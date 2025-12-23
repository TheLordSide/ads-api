<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\User;

class AdService
{

    //lister les commentaires avec une pagination de 10 par pages
    public function list(array $filter = [])
    {
        return Ad::latest()
            ->paginate($filter['per_page'] ?? 10);
    }

    //creer une annonce
    public function create(array $data, User $user)
    {
        $data['user_id'] = $user->id;
        return Ad::create($data);
    }

    //mettre a jour une annonce
    public function update(Ad $ad, array $data)
    {

        $ad->update($data);
        return $ad;
    }

    //supprimer une annonce
    public function delete(Ad $ad)
    {
        $ad->delete();
    }
}
