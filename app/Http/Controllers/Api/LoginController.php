<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * @group Auth
 **/
class LoginController
{
    /**
     * Login
     *
     * @bodyParam email string required Email. Example: user@example.com
     *
     * @bodyParam password string required Password. Example: secret123
     *
     * @responseFile 200 responses/auth.login.json
     * @responseFile 422 responses/auth.login.422.json
     *
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $randomAccessToken = Str::random(60);

        return response()->json([
            'message' => 'Mocked login successful',
            'access_token' => $randomAccessToken,
            'token_type' => 'Bearer',
        ], JsonResponse::HTTP_OK);
    }
}
