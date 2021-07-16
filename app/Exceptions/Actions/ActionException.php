<?php

namespace App\Exceptions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ActionException extends Exception
{
    protected int $status = Response::HTTP_UNPROCESSABLE_ENTITY;

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
