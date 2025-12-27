<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public static function throwError(string $message, int $status)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}
