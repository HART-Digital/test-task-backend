<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\APIController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Services\Auth\AuthService;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends APIController
{
    private AuthService $authService;
    private RegistrationService $registrationService;

    public function __construct(AuthService $authService, RegistrationService $registrationService)
    {
        $this->authService = $authService;
        $this->registrationService = $registrationService;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Login"},
     *     summary="Войти в систему",
     *     description="Войти в систему",
     *      @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="password",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Успешно",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                 ),
     *            ),
     *          ),
     *
     *        )
     *     )
     *   )
     * )
     */
    public function login(LoginRequest $request): Response
    {
        $data = $this->authService->login($request->only('email', 'password'));

        return response()->json($data);
    }

    public function logout(): Response
    {
        $message = $this->authService->logout();
        return response()->json($message);
    }

    public function register(RegistrationRequest $request): Response
    {
        $user = $this->registrationService->register($request->getDTO());

        return response()->json($user);
    }

    public function me(Request $request): Response
    {
        return response()->json($request->user());
    }
}
