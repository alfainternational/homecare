<?php

namespace App\Services\Payment\Gateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Tamara Gateway — BNPL: Pay Later / Split in 3-4 installments.
 * Docs: https://docs.tamara.co/
 */
class TamaraGateway implements PaymentGatewayInterface
{
    private const TEST_URL = 'https://api-sandbox.tamara.co';
    private const LIVE_URL = 'https://api.tamara.co';

    public function __construct(private readonly ?PaymentGateway $config) {}

    private function baseUrl(): string
    {
        return $this->config?->isTestMode() ? self::TEST_URL : self::LIVE_URL;
    }

    public function initiate(PaymentTransaction $transaction, array $extra = []): array
    {
        if (!$this->config) {
            return ['success' => false, 'message' => 'بوابة Tamara غير مهيأة.', 'redirect_url' => null, 'checkout_id' => null];
        }

        try {
            $response = Http::withToken($this->config->api_key)
                ->post($this->baseUrl() . '/checkout', [
                    'order_reference_id' => $transaction->reference,
                    'order_number'       => $transaction->reference,
                    'total_amount'       => ['amount' => (string) $transaction->amount, 'currency' => 'SAR'],
                    'description'        => $extra['description'] ?? 'WarmConcierge Payment',
                    'country_code'       => 'SA',
                    'payment_type'       => $extra['payment_type'] ?? 'PAY_BY_INSTALMENTS',
                    'instalments'        => $extra['instalments'] ?? 3,
                    'consumer' => [
                        'first_name'   => $extra['first_name'] ?? '',
                        'last_name'    => $extra['last_name']  ?? '',
                        'phone_number' => $extra['phone']      ?? '',
                        'email'        => $extra['email']      ?? '',
                    ],
                    'billing_address' => [
                        'city'         => $extra['city']    ?? 'Riyadh',
                        'country_code' => 'SA',
                    ],
                    'items' => $extra['items'] ?? [],
                    'merchant_url' => [
                        'success'      => $transaction->redirect_url . '?status=success',
                        'failure'      => $transaction->redirect_url . '?status=failure',
                        'cancel'       => $transaction->redirect_url . '?status=cancel',
                        'notification' => route('payment.webhook', ['gateway' => 'tamara']),
                    ],
                ]);

            $body = $response->json();

            if (isset($body['checkout_id'])) {
                return [
                    'success'      => true,
                    'checkout_id'  => $body['checkout_id'],
                    'redirect_url' => $body['checkout_url'],
                    'message'      => 'تم إنشاء جلسة Tamara بنجاح.',
                ];
            }

            Log::warning('Tamara initiate failed', ['response' => $body]);
            return ['success' => false, 'message' => 'فشل في إنشاء جلسة الدفع عبر Tamara.', 'redirect_url' => null, 'checkout_id' => null];

        } catch (\Exception $e) {
            Log::error('Tamara exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'خطأ في الاتصال بـ Tamara.', 'redirect_url' => null, 'checkout_id' => null];
        }
    }

    public function verify(PaymentTransaction $transaction, array $callbackData): bool
    {
        $orderId = $callbackData['order_id'] ?? null;
        if (!$orderId) {
            $transaction->markFailed($callbackData);
            return false;
        }

        try {
            $response = Http::withToken($this->config->api_key)
                ->get($this->baseUrl() . "/orders/{$orderId}");

            $body   = $response->json();
            $status = $body['status'] ?? '';

            if (in_array($status, ['approved', 'fully_captured'])) {
                // Authorise and capture
                Http::withToken($this->config->api_key)
                    ->post($this->baseUrl() . "/orders/{$orderId}/authorise");

                Http::withToken($this->config->api_key)
                    ->post($this->baseUrl() . "/payments/capture", [
                        'order_id' => $orderId,
                        'total_amount' => ['amount' => (string) $transaction->amount, 'currency' => 'SAR'],
                    ]);

                $transaction->markPaid($orderId, $body);
                return true;
            }

            $transaction->markFailed($body);
            return false;

        } catch (\Exception $e) {
            Log::error('Tamara verify exception', ['error' => $e->getMessage()]);
            $transaction->markFailed(['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool
    {
        try {
            $response = Http::withToken($this->config->api_key)
                ->post($this->baseUrl() . "/payments/refund", [
                    'order_id'     => $transaction->gateway_reference,
                    'total_amount' => [
                        'amount'   => (string) ($amount ?? $transaction->amount),
                        'currency' => 'SAR',
                    ],
                    'items' => [],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Tamara refund exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
