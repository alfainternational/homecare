<?php

namespace App\Services\Payment\Gateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Tabby Gateway — BNPL: 4 interest-free installments.
 * Docs: https://docs.tabby.ai/
 */
class TabbyGateway implements PaymentGatewayInterface
{
    private const TEST_URL = 'https://api.tabby.ai/api/v2';
    private const LIVE_URL = 'https://api.tabby.ai/api/v2';

    public function __construct(private readonly ?PaymentGateway $config) {}

    public function initiate(PaymentTransaction $transaction, array $extra = []): array
    {
        if (!$this->config) {
            return ['success' => false, 'message' => 'بوابة Tabby غير مهيأة.', 'redirect_url' => null, 'checkout_id' => null];
        }

        $installment = round($transaction->amount / 4, 2);

        try {
            $response = Http::withToken($this->config->api_key)
                ->post(self::TEST_URL . '/checkout', [
                    'payment' => [
                        'amount'      => (string) $transaction->amount,
                        'currency'    => 'SAR',
                        'description' => $extra['description'] ?? 'WarmConcierge Payment',
                        'buyer'       => [
                            'phone'  => $extra['phone'] ?? '',
                            'email'  => $extra['email'] ?? '',
                            'name'   => $extra['name']  ?? '',
                        ],
                        'order' => [
                            'reference_id' => $transaction->reference,
                            'items' => $extra['items'] ?? [],
                        ],
                        'buyer_history' => [
                            'registered_since' => $extra['registered_since'] ?? now()->toIso8601String(),
                            'loyalty_level'    => 0,
                        ],
                        'order_history' => $extra['order_history'] ?? [],
                    ],
                    'merchant_code' => $this->config->merchant_id,
                    'lang'          => 'ar',
                    'merchant_urls' => [
                        'success'  => $transaction->redirect_url . '?status=success',
                        'cancel'   => $transaction->redirect_url . '?status=cancel',
                        'failure'  => $transaction->redirect_url . '?status=failure',
                    ],
                ]);

            $body = $response->json();

            if (isset($body['id']) && $body['status'] === 'created') {
                return [
                    'success'      => true,
                    'checkout_id'  => $body['id'],
                    'redirect_url' => $body['configuration']['available_products']['installments'][0]['web_url'] ?? null,
                    'installment'  => $installment,
                    'message'      => "قسّم {$transaction->amount} ر.س على 4 دفعات × {$installment} ر.س",
                ];
            }

            Log::warning('Tabby initiate failed', ['response' => $body]);
            return ['success' => false, 'message' => 'فشل في إنشاء جلسة الدفع عبر Tabby.', 'redirect_url' => null, 'checkout_id' => null];

        } catch (\Exception $e) {
            Log::error('Tabby exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'خطأ في الاتصال بـ Tabby.', 'redirect_url' => null, 'checkout_id' => null];
        }
    }

    public function verify(PaymentTransaction $transaction, array $callbackData): bool
    {
        $paymentId = $callbackData['payment_id'] ?? null;
        if (!$paymentId) {
            $transaction->markFailed($callbackData);
            return false;
        }

        try {
            $response = Http::withToken($this->config->api_key)
                ->get(self::TEST_URL . "/payments/{$paymentId}");

            $body = $response->json();

            if (($body['status'] ?? '') === 'AUTHORIZED') {
                // Capture the payment
                Http::withToken($this->config->api_key)
                    ->post(self::TEST_URL . "/payments/{$paymentId}/captures", [
                        'amount' => (string) $transaction->amount,
                    ]);

                $transaction->markPaid($paymentId, $body);
                return true;
            }

            $transaction->markFailed($body);
            return false;

        } catch (\Exception $e) {
            Log::error('Tabby verify exception', ['error' => $e->getMessage()]);
            $transaction->markFailed(['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool
    {
        try {
            $response = Http::withToken($this->config->api_key)
                ->post(self::TEST_URL . "/payments/{$transaction->gateway_reference}/refunds", [
                    'amount' => (string) ($amount ?? $transaction->amount),
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Tabby refund exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
