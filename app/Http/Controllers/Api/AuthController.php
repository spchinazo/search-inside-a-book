<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\GenericResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());
        return new GenericResponse([
            'status'    => 'OK',
            'message'   => __('app.auth_success'),
            'code'      => Response::HTTP_OK,
            'payload'   => $result
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return new GenericResponse([
            'status'    => 'OK',
            'message'   => __('app.logout_success'),
            'code'      => Response::HTTP_OK,
            'payload'   => true
        ]);
    }
}
