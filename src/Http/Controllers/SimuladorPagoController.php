<?php

namespace App\Http\Controllers;

use App\Gateway\SimuladorPagoGateway;
use App\Http\Request;
use App\Http\Response;
use App\Services\MercadoPagoService;

class SimuladorPagoController
{
    public function store(Request $request, Response $response){
 
        $client[] = true;
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
            'data' => "Acesso autorizado ao Simulador Pago"
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