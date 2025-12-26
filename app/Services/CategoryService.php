<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    //liste des categories
    public function list()
    {
        return Category::all();
    }

    //creation d'une categorie
    public function create(array $data)
    {
        return Category::create($data);
    }

    //mise a jour d'une categorie
    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    //suppression d'une categorie
    public function delete(Category $category)
    {
        $category->delete();
    }
    
}
