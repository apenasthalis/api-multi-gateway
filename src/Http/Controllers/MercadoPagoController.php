<?php

namespace App\Http\Controllers;

use App\Gateway\MercadoPagoGateway;
use App\Http\Request;
use App\Http\Response;
use App\Services\MercadoPagoService;

class MercadoPagoController
{
    public function store(Request $request, Response $response){
        $mercadoPagoService = new MercadoPagoService();
        $body = $request->body();
        $client = $mercadoPagoService->payment($body);
        if (isset($client['error'])) {
            return $response::json([
                    'error' => true,
                    'success' => false, 
                    'message' => $client['error']
                ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true, 
            'data' => $client
        ], 200);
        
        return;
    }

    public function verify(){
        $response = new Response();
        $mercadoPagoService = new MercadoPagoService();
        $payments = $mercadoPagoService->getPaymentById();

        $response::json([
            'error' => false,
            'success' => true, 
            'data' => $payments
        ], 200);
        
        return;
    }
}