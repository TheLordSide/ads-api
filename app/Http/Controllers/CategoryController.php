<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    // affichage de la liste des categories
    public function index()
    {
        try {
            return CategoryResource::collection(
                $this->categoryService->list()
            );
        } catch (\Exception) {
            return ErrorResource::throwError('Error fetching categories', 500);
        }
    }

    //ajout d'une categorie
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:categories,name',
            ]);

            if ($validator->fails()) {
                return ErrorResource::throwError('Category name already exists', 422);
            }

            $category = $this->categoryService->create($validator->validated());

            return new CategoryResource($category);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error creating category', 500);
        }
    }


    //mise a jour d'une categorie
    public function update(Request $request, int $id)
    {
        try {
            $category = Category::findOrFail($id);

            // Vérifier si le nom est fourni
            $data = $request->validate([
                'name' => 'required|string',
            ]);

            // Vérifier si le nom existe déjà pour une autre catégorie
            $nameExists = Category::where('name', $data['name'])
                ->where('id', '!=', $id)
                ->exists();

            if ($nameExists) {
                return ErrorResource::throwError('Category name already exists', 422);
            }

            $category = $this->categoryService->update($category, $data);

            return new CategoryResource($category);
        } catch (ModelNotFoundException) {
            return ErrorResource::throwError('Category not found', 404);
        } catch (ValidationException) {
            return ErrorResource::throwError('Name is required', 422);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error updating category', 500);
        }
    }

    // suppression d'une categorie 
    public function destroy(int $id)
    {
        try {
            $category = Category::findOrFail($id);

            // Vérifier si des annonces utilisent cette catégorie
            $hasAds = $category->ad()->exists();

            if ($hasAds) {
                return ErrorResource::throwError('Cannot delete category with existing ads', 422);
            }

            $this->categoryService->delete($category);

            return response()->noContent();
            
        } catch (ModelNotFoundException) {
            return ErrorResource::throwError('Category not found', 404);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error deleting category', 500);
        }
    }
}
