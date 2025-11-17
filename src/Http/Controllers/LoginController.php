<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\LoginService;

class LoginController
{
    public function login(Request $request, Response $response)
    {
        $body = $request::body();

        $clientService = LoginService::auth($body);

        if (isset($clientService['error'])) {
            return $response::json([
                'error'   => true,
                'success' => false,
                'message' => $clientService['error']
            ], 400);
        }

        $response::json([
            'error'   => false,
            'success' => true,
            'jwt'     => $clientService
        ], 200);
        return;
    }

}