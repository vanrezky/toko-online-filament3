<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, string $gateway, PaymentGatewayService $paymentGatewayService)
    {
        Log::info("Payment webhook received for {$gateway}", [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $result = $paymentGatewayService->handleWebhook($request->all());

            if (!$result->success) {
                Log::warning("Payment webhook processing failed for {$gateway}", ['message' => $result->message]);
                return response()->json(['status' => 'error', 'message' => $result->message], 400);
            }

            if ($result->action === \App\Services\Gateways\DTOs\WebhookResult::ACTION_PROCESS && $result->transactionId) {
                $transaction = Transaction::where('uuid', $result->transactionId)->first();
                
                if ($transaction) {
                    if ($result->status && $transaction->status !== $result->status) {
                        $transaction->update(['status' => $result->status]);
                        Log::info("Transaction {$result->transactionId} status updated to {$result->status} via {$gateway} webhook.");
                    }
                } else {
                    Log::warning("Transaction {$result->transactionId} not found for {$gateway} webhook.");
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error handling {$gateway} webhook: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
