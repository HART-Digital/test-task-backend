<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\APIController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\Auth\ResettingPasswordService;
use Symfony\Component\HttpFoundation\Response;

class ResettingPasswordController extends APIController
{
    private ResettingPasswordService $resettingPasswordService;

    public function __construct(ResettingPasswordService $resettingPasswordService)
    {
        $this->resettingPasswordService = $resettingPasswordService;
    }

    public function forgot(ForgotPasswordRequest $request): Response
    {
        $this->resettingPasswordService->forgot($request->get('email'));

        return response()->noContent();
    }

    public function reset(ResetPasswordRequest $request): Response
    {
        $this->resettingPasswordService->resetPasswordAndSendEmail($request->only('email', 'token'));

        return response()->json(['message' => 'Password changed. Check your mail.']);
    }
}
