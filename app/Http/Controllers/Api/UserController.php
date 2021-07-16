<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ChangeRoleRequest;
use App\Services\Auth\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list(): Response
    {
        $users = $this->userService->getList();

        return response()->json($users);
    }

    public function changeRole(ChangeRoleRequest $request): Response
    {
        $user = $this->userService->changeRole($request->get('id'), $request->get('roles'));

        return response()->json($user);
    }
}
