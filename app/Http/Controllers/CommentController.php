<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\CommentService;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\CommentResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    // Retourner la liste des commentaires par annonce
    public function index(int $id)
    {
        try {
            $ad = Ad::findOrFail($id);

            $comments = $this->commentService->listByAd($ad);

            //verification si des commentaires existent pour une annonce

            if ($comments->isEmpty()) {
                return ErrorResource::throwError('No comments found for this ad', 404);
            }

            return CommentResource::collection($comments);
        } catch (ModelNotFoundException) {
            return ErrorResource::throwError('Ad not found', 404);
        } catch (\Exception) {
            return ErrorResource::throwError('Error fetching comments', 500);
        }
    }

    // Création d'un commentaire
    public function store(Request $request, int $id)
    {
        try {
            $ad = Ad::findOrFail($id);

            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $comment = $this->commentService->create($ad, $request->user(), $validated['content']);

            return new CommentResource($comment);
        } catch (ModelNotFoundException) {
            return ErrorResource::throwError('Ad not found', 404);
        } catch (ValidationException) {
            return ErrorResource::throwError('Content is required', 422);
        } catch (\Exception) {
            return ErrorResource::throwError('Error creating comment', 500);
        }
    }

    // Suppression d'un commentaire
    public function destroy(int $id)
    {
        try {
            // Récupérer le commentaire manuellement
            $comment = Comment::find($id);
            if (!$comment) {
                return ErrorResource::throwError("Comment not found", 404);
            }

            // Vérifier l'autorisation
            $this->authorize('delete', $comment);

            // Suppression via le service
            $this->commentService->delete($comment);

            return response()->noContent();
        } catch (AuthorizationException) {
            return ErrorResource::throwError('Unauthorized | You cannot delete this comment', 403);
        } catch (\Exception) {
            return ErrorResource::throwError('Error deleting comment', 500);
        }
    }
}
