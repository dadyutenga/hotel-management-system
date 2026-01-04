<?php

namespace App\Services;

use App\Models\ClickpesaTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickpesaService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.clickpesa.base_url', 'https://api.clickpesa.com');
        $this->clientId = config('services.clickpesa.client_id');
        $this->apiKey = config('services.clickpesa.api_key');
    }

    /**
     * Generate an authentication token (Bearer token).
     *
     * @return string|null
     */
    public function generateToken(): ?string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'client-id' => $this->clientId,
                'api-key' => $this->apiKey,
            ])->post("{$this->baseUrl}/third-parties/generate-token");

            if ($response->successful()) {
                return $response->json()['token'];
            }

            Log::error('ClickPesa Token Generation Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('ClickPesa Token Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate USSD Push Payment.
     *
     * @param array $data
     * @return array
     */
    public function initiatePayment(array $data): array
    {
        $token = $this->generateToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to generate authentication token.',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/third-parties/payments/initiate-ussd-push", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('ClickPesa USSD Push Failed: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Payment initiation failed.',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('ClickPesa USSD Push Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred during payment initiation.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query Payment Status.
     *
     * @param string $transactionId
     * @return array
     */
    public function queryStatus(string $transactionId): array
    {
        $token = $this->generateToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to generate authentication token.',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/third-parties/payments/query-payment-status", [
                    'transaction_id' => $transactionId,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('ClickPesa Query Status Failed: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Failed to query payment status.',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('ClickPesa Query Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred during status query.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
