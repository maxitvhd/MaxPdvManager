<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SistemaConfiguracao;
use Exception;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected $token;
    protected $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $config = SistemaConfiguracao::first();
        if ($config && $config->mercadopago_access_token) {
            $this->token = $config->mercadopago_access_token;
        }
    }

    public function isConfigured()
    {
        return !empty($this->token);
    }

    protected function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'X-Idempotency-Key' => (string) Str::uuid()
        ];
    }

    /**
     * Cria uma fatura Checkout Pro
     */
    public function criarPreferencia($dados)
    {
        if (!$this->isConfigured())
            throw new Exception("Mercado Pago não configurado.");

        $payload = [
            "items" => [
                [
                    "title" => $dados['descricao'],
                    "quantity" => 1,
                    "unit_price" => (float) $dados['valor'],
                    "currency_id" => "BRL"
                ]
            ],
            "external_reference" => (string) $dados['external_reference'],
            "back_urls" => [
                "success" => route('pagamentos.sucesso'),
                "failure" => route('pagamentos.falha'),
                "pending" => route('pagamentos.pendente')
            ],
            "auto_return" => "approved",
            "notification_url" => config('app.url') . "/api/webhooks/mercadopago"
        ];

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/checkout/preferences", $payload);

        if ($response->failed()) {
            throw new Exception("Erro ao criar preferência MP: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Gera PIX direto (Checkout API)
     */
    public function gerarPix($valor, $descricao, $email_pagador, $external_reference)
    {
        if (!$this->isConfigured())
            throw new Exception("Mercado Pago não configurado.");

        $payload = [
            "transaction_amount" => (float) $valor,
            "description" => $descricao,
            "payment_method_id" => "pix",
            "payer" => [
                "email" => $email_pagador
            ],
            "external_reference" => (string) $external_reference,
            "notification_url" => config('app.url') . "/api/webhooks/mercadopago"
        ];

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/v1/payments", $payload);

        if ($response->failed()) {
            throw new Exception("Erro ao gerar PIX MP: " . $response->body());
        }

        return $response->json();
    }

    public function consultarPagamento($id)
    {
        if (!$this->isConfigured())
            throw new Exception("Mercado Pago não configurado.");

        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/v1/payments/{$id}");

        return $response->successful() ? $response->json() : null;
    }

    public function estornarPagamento($id)
    {
        if (!$this->isConfigured())
            throw new Exception("Mercado Pago não configurado.");

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/v1/payments/{$id}/refunds");

        if ($response->failed()) {
            throw new Exception("Erro ao estornar: " . $response->body());
        }

        return $response->json();
    }
}
