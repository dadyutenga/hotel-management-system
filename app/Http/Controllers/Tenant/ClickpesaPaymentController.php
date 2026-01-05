<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ClickpesaTransaction;
use App\Services\ClickpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ClickpesaPaymentController extends Controller
{
    protected $clickpesaService;

    public function __construct(ClickpesaService $clickpesaService)
    {
        $this->clickpesaService = $clickpesaService;
    }

    /**
     * Initiate a USSD payment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'msisdn' => 'required|string',
            'provider' => 'required|string', // e.g., AIRTEL, VODACOM, TIGO, HALOPESA
            'currency' => 'nullable|string|in:TZS,USD',
            'reference' => 'nullable|string',
        ]);

        $reference = $validated['reference'] ?? 'ORD-' . strtoupper(Str::random(10));
        $currency = $validated['currency'] ?? 'TZS';

        // Prepare data for ClickPesa
        $paymentData = [
            'reference' => $reference,
            'amount' => $validated['amount'],
            'currency' => $currency,
            'msisdn' => $validated['msisdn'],
            'provider' => $validated['provider'],
            'callback_url' => route('tenant.payments.clickpesa.callback'), // Webhook URL
        ];

        // Call Service
        $result = $this->clickpesaService->initiatePayment($paymentData);

        if ($result['success']) {
            // Store transaction in DB
            $transaction = ClickpesaTransaction::create([
                'transaction_id' => $result['data']['transaction_id'] ?? null,
                'reference' => $reference,
                'amount' => $validated['amount'],
                'currency' => $currency,
                'msisdn' => $validated['msisdn'],
                'provider' => $validated['provider'],
                'status' => 'pending', // or 'processing' based on ClickPesa response
                'request_payload' => $paymentData,
                'response_payload' => $result,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully.',
                'data' => [
                    'transaction_id' => $transaction->id, // Our internal UUID
                    'clickpesa_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], 400);
    }

    /**
     * Check payment status.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(string $id)
    {
        $transaction = ClickpesaTransaction::where('id', $id)->firstOrFail();

        if ($transaction->transaction_id) {
            $result = $this->clickpesaService->queryStatus($transaction->transaction_id);

            if ($result['success'] && isset($result['data']['status'])) {
                $status = strtolower($result['data']['status']); // ClickPesa returns UPPERCASE usually

                // Update local status
                $transaction->update([
                    'status' => $status,
                    'response_payload' => array_merge($transaction->response_payload ?? [], ['status_check' => $result]),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
            ],
        ]);
    }

    /**
     * Handle Webhook Callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        Log::info('ClickPesa Callback Received', $request->all());

        $data = $request->all();

        // Example payload: {"transaction_id": "...", "status": "SUCCESS", ...}
        if (isset($data['transaction_id'])) {
            $transaction = ClickpesaTransaction::where('transaction_id', $data['transaction_id'])
                ->orWhere('reference', $data['reference'] ?? '')
                ->first();

            if ($transaction) {
                $status = strtolower($data['status'] ?? 'pending');
                $transaction->update([
                    'status' => $status,
                    'response_payload' => array_merge($transaction->response_payload ?? [], ['callback' => $data]),
                ]);

                Log::info("Transaction {$transaction->id} updated to {$status}");
            } else {
                Log::warning("Transaction not found for callback: " . json_encode($data));
            }
        }

        return response()->json(['success' => true]);
    }
}
