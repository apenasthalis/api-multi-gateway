<?php

namespace App\Gateway;

use App\Models\MercadoPago;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoGateway
{
    protected string $baseUrl;
    protected string $accessToken;
    protected PaymentClient $client;
    // protected $token;

    public function __construct()
    {
        $token = getenv('MERCADOPAGO_ACCESS_TOKEN');
        MercadoPagoConfig::setAccessToken($token);
        $this->baseUrl = getenv('MERCADOPAGO_BASEURL');
        $this->accessToken = getenv('MERCADOPAGO_ACCESS_TOKEN');
        $this->client = new PaymentClient();
    }

    public function makePayment(string $amount, $client, $paymentMethod = 'pix')
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new \InvalidArgumentException('Amount must be a positive number.');
        }
        if (empty($client['email'])) {
            throw new \InvalidArgumentException('User email is required for payment.');
        }
        $amount = '1.00';
        $idempotencyKey = $client['id'] . '-' . time();
        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(['X-Idempotency-Key: ' . $idempotencyKey]);

        $paymentResponse = $this->client->create([
            'transaction_amount' => (float) $amount,
            'payment_method_id' => $paymentMethod,
            'payer' => [
                'email' => $client['email'],
            ],
        ], $request_options);
        
        $mercadoPago = new MercadoPago();
        $insertMercadoPago = $mercadoPago->insert([
            'client_id' => $client['id'],
            'payment_id' => $paymentResponse->id,
            'status' => $paymentResponse->status,
            'status_detail' => $paymentResponse->status_detail,
            'transaction_amount' => $paymentResponse->transaction_amount,
            'external_reference' => $paymentResponse->external_reference,
            'date_created' => $paymentResponse->date_created,
            'date_approved' => $paymentResponse->date_approved,
            'date_last_updated' => $paymentResponse->date_last_updated,
            'date_of_expiration' => $paymentResponse->date_of_expiration,
            'qr_code_base64' => $paymentResponse->point_of_interaction->transaction_data->qr_code_base64,
            'qr_code' => $paymentResponse->point_of_interaction->transaction_data->qr_code,
            'ticket_url' => $paymentResponse->point_of_interaction->transaction_data->ticket_url,
        ]);
        return [
            $insertMercadoPago,
            'payment_id' => $paymentResponse->id,
            'status' => $paymentResponse->status,
            'ticket_url' => $paymentResponse->point_of_interaction->transaction_data->ticket_url,
        ];
    }

    /**
     * Consulta o status de um pagamento por ID usando a API REST do Mercado Pago.
     *
     * @return array{status: string, status_detail: ?string, raw: \MercadoPago\Resources\Payment}
     *
     * @throws \RuntimeException
     */
    public function getPaymentStatus(int $paymentId): array
    {
        $payment = $this->client->get($paymentId);

        if (!$payment) {
            throw new \RuntimeException("Falha ao consultar pagamento MP");
        }

        return [
            'status' => $payment->status ?? 'unknown',
            'status_detail' => $payment->status_detail ?? null,
            'raw' => $payment,
        ];
    }
}