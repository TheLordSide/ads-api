<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Routes protégées JWT
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

/*
|--------------------------------------------------------------------------
| Ads Routes
|--------------------------------------------------------------------------
*/
Route::prefix('ads')->group(function () {
    Route::get('/search', [SearchController::class, 'index']);
    Route::get('/', [AdController::class, 'index']);      // Liste + pagination      
    Route::get('/{id}', [AdController::class, 'show']);    // Détail d'une annonce    

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [AdController::class, 'store']);      // Création d'une annonce
        Route::put('/{id}', [AdController::class, 'update']);   //Update (auteur seulement)
        Route::delete('/{id}', [AdController::class, 'destroy']); // Suppression (auteur seulement)
    });
});


/*
|--------------------------------------------------------------------------
| Categories Routes
|--------------------------------------------------------------------------
*/
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // Liste catégories

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Comments Routes
|--------------------------------------------------------------------------
*/
Route::prefix('ads/{adId}/comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']); // Liste commentaires

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [CommentController::class, 'store']); // Ajouter commentaire
    });
});

// Supprimer un commentaire par ID (auth required)
Route::middleware('auth:api')->delete('comments/{id}', [CommentController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Orders Routes
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->middleware('auth:api')->group(function () {
    Route::post('/', [OrderController::class, 'store']);       // Passer commande
    Route::get('/me', [OrderController::class, 'index']);   // Liste commandes (acheteur ou vendeur)
    Route::put('/{id}/confirm', [OrderController::class, 'confirm']); // Confirmer commande (vendeur)
    Route::put('/{id}/cancel', [OrderController::class, 'cancel']);   // Annuler commande (acheteur ou vendeur)
});
