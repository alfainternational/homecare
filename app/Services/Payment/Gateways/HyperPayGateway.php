<?php

namespace App\Services\Payment\Gateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HyperPay Gateway — supports Mada, Visa/MC, Apple Pay (PPRO channel).
 * Docs: https://wordpresshyperpay.docs.oppwa.com/
 */
class HyperPayGateway implements PaymentGatewayInterface
{
    private const TEST_URL = 'https://eu-test.oppwa.com/v1';
    private const LIVE_URL = 'https://oppwa.com/v1';

    public function __construct(private readonly ?PaymentGateway $config) {}

    public function initiate(PaymentTransaction $transaction, array $extra = []): array
    {
        if (!$this->config) {
            return ['success' => false, 'message' => 'بوابة الدفع غير مهيأة.', 'redirect_url' => null, 'checkout_id' => null];
        }

        $baseUrl   = $this->config->isTestMode() ? self::TEST_URL : self::LIVE_URL;
        $entityId  = $this->config->entity_id;
        $channel   = $this->resolveChannel($transaction->gateway_code);

        try {
            $response = Http::withToken($this->config->api_key)
                ->asForm()
                ->post("{$baseUrl}/checkouts", [
                    'entityId'          => $entityId,
                    'amount'            => number_format($transaction->amount, 2, '.', ''),
                    'currency'          => 'SAR',
                    'paymentType'       => 'DB',
                    'merchantTransactionId' => $transaction->reference,
                    'customer.email'    => $extra['email'] ?? '',
                    'customer.mobile'   => $extra['phone'] ?? '',
                    'billing.country'   => 'SA',
                    'shopperResultUrl'  => $transaction->redirect_url,
                ]);

            $body = $response->json();

            if (isset($body['id'])) {
                $jsUrl = $this->config->isTestMode()
                    ? "https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId={$body['id']}"
                    : "https://oppwa.com/v1/paymentWidgets.js?checkoutId={$body['id']}";

                return [
                    'success'      => true,
                    'checkout_id'  => $body['id'],
                    'js_url'       => $jsUrl,
                    'channel'      => $channel,
                    'redirect_url' => route('payment.checkout-widget', ['reference' => $transaction->reference]),
                    'message'      => 'تم إنشاء جلسة الدفع بنجاح.',
                ];
            }

            Log::warning('HyperPay initiate failed', ['response' => $body]);
            return ['success' => false, 'message' => $body['result']['description'] ?? 'فشل في إنشاء جلسة الدفع.', 'redirect_url' => null, 'checkout_id' => null];

        } catch (\Exception $e) {
            Log::error('HyperPay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'خطأ في الاتصال ببوابة الدفع.', 'redirect_url' => null, 'checkout_id' => null];
        }
    }

    public function verify(PaymentTransaction $transaction, array $callbackData): bool
    {
        $baseUrl  = $this->config->isTestMode() ? self::TEST_URL : self::LIVE_URL;
        $entityId = $this->config->entity_id;
        $id       = $callbackData['id'] ?? $transaction->checkout_id;

        try {
            $response = Http::withToken($this->config->api_key)
                ->get("{$baseUrl}/checkouts/{$id}/payment", ['entityId' => $entityId]);

            $body = $response->json();
            $code = $body['result']['code'] ?? '';

            // HyperPay success pattern: 000.000.000 or 000.100.110
            if (preg_match('/^(000\.000\.|000\.100\.1)/', $code)) {
                $transaction->markPaid($body['id'] ?? '', $body);
                return true;
            }

            $transaction->markFailed($body);
            return false;

        } catch (\Exception $e) {
            Log::error('HyperPay verify exception', ['error' => $e->getMessage()]);
            $transaction->markFailed(['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool
    {
        $baseUrl  = $this->config->isTestMode() ? self::TEST_URL : self::LIVE_URL;
        $refundAmt = $amount ?? $transaction->amount;

        try {
            $response = Http::withToken($this->config->api_key)
                ->asForm()
                ->post("{$baseUrl}/payments/{$transaction->gateway_reference}", [
                    'entityId'    => $this->config->entity_id,
                    'amount'      => number_format($refundAmt, 2, '.', ''),
                    'currency'    => 'SAR',
                    'paymentType' => 'RF',
                ]);

            $body = $response->json();
            $code = $body['result']['code'] ?? '';
            return preg_match('/^(000\.000\.|000\.100\.1)/', $code) === 1;

        } catch (\Exception $e) {
            Log::error('HyperPay refund exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function resolveChannel(string $code): string
    {
        return match($code) {
            'mada'      => 'MADA',
            'apple_pay' => 'APPLEPAY',
            default     => 'VISA MASTER',
        };
    }
}
