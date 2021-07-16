<?php

namespace App\Exceptions\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ServiceException extends \Exception
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function render(): JsonResponse
    {
        return response()->json(
            [
                'message' => $this->getMessage(),
            ],
            $this->code,
        );
    }
}
