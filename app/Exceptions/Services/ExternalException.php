<?php

namespace App\Exceptions\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExternalException extends \Exception
{
    protected int $status = Response::HTTP_FORBIDDEN;

    public function render(): JsonResponse
    {
        return response()->json(
            [
                'message' => $this->getMessage(),
            ],
            $this->status,
        );
    }
}
