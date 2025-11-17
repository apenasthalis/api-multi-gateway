<?php

namespace App\Services;

use App\Gateway\MercadoPagoGateway;
use App\Http\Middlewares\Jwt;
use App\Models\Login;
use App\Models\MercadoPago;
use App\Utils\Validator;

class MercadoPagoService
{
    public function payment($data)
    {
        try {
            $mercadoPagoGateway = new MercadoPagoGateway();
            $fields = Validator::validate([
                'amount' => $data['amount'] ?? '',
                'email' => $data['email'] ?? '',
            ]);
            $mercadoPagoModel = new MercadoPago();
            $client = $mercadoPagoModel->getClientByEmail($fields);
            $payment = $mercadoPagoGateway->makePayment($fields['amount'],$client);
            if (!$payment) return ['error' => 'Sorry, we could not process your payment.'];
            return $payment;
        } catch (\PDOException $e) {
            if ($e->errorInfo[0] === '08006') return ['error' => 'Sorry, we could not connect to the database.'];
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getPaymentById()
    {
        try {
            $mercadoPagoModel = new MercadoPago();
            $url = $_SERVER['REQUEST_URI'];
            $url = strtok($url, '?');
            $parts = explode('/', trim($url, '/'));
            $id = $parts[1];
            $mercadoPagoGateway = new MercadoPagoGateway();
            $paymentsDataBase = $mercadoPagoModel->getPaymentById($id);
            $paymentStatus = $mercadoPagoGateway->getPaymentStatus($paymentsDataBase[0]['payment_id']);
            $paymentUpdated = $mercadoPagoModel->update([
                'payment_id' => $paymentsDataBase[0]['payment_id'],
                'status' => $paymentStatus['status'],
                'status_detail' => $paymentStatus['status_detail'],
            ]);
            return $paymentsDataBase;
        } catch (\PDOException $e) {
            if ($e->errorInfo[0] === '08006') return ['error' => 'Sorry, we could not connect to the database.'];
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}