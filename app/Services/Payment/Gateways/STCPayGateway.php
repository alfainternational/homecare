<?php

namespace App\Services\Payment\Gateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * STC Pay Gateway — Saudi digital wallet.
 * Docs: https://developer.stcpay.com.sa/
 */
class STCPayGateway implements PaymentGatewayInterface
{
    private const TEST_URL = 'https://api-sandbox.stcpay.com.sa/payment/v1';
    private const LIVE_URL = 'https://api.stcpay.com.sa/payment/v1';

    public function __construct(private readonly ?PaymentGateway $config) {}

    private function baseUrl(): string
    {
        return $this->config?->isTestMode() ? self::TEST_URL : self::LIVE_URL;
    }

    public function initiate(PaymentTransaction $transaction, array $extra = []): array
    {
        if (!$this->config) {
            return ['success' => false, 'message' => 'بوابة STC Pay غير مهيأة.', 'redirect_url' => null, 'checkout_id' => null];
        }

        try {
            $response = Http::withHeaders([
                'X-ClientCode'    => $this->config->merchant_id,
                'X-ClientSecret'  => $this->config->secret_key,
                'Content-Type'    => 'application/json',
            ])->post($this->baseUrl() . '/directPayment/init', [
                'MerchantID'        => $this->config->merchant_id,
                'BranchID'          => $this->config->settings['branch_id'] ?? '1',
                'TellerID'          => $this->config->settings['teller_id'] ?? '1',
                'DeviceID'          => $this->config->settings['device_id'] ?? '1',
                'RefNum'            => $transaction->reference,
                'BillNumber'        => $transaction->reference,
                'Amount'            => $transaction->amount,
                'CurrencyCode'      => 'SAR',
                'MobileNo'          => $extra['phone'] ?? '',
                'ReturnUrl'         => $transaction->redirect_url,
            ]);

            $body = $response->json();

            if (($body['StatusCode'] ?? '') === '0000') {
                return [
                    'success'      => true,
                    'checkout_id'  => $body['SessionID'] ?? null,
                    'redirect_url' => $body['OtpReference']
                        ? route('payment.stcpay-otp', ['reference' => $transaction->reference])
                        : null,
                    'otp_ref'      => $body['OtpReference'] ?? null,
                    'message'      => 'تم إرسال رمز التحقق إلى جوالك.',
                ];
            }

            Log::warning('STC Pay initiate failed', ['response' => $body]);
            return ['success' => false, 'message' => $body['StatusDesc'] ?? 'فشل في الاتصال بـ STC Pay.', 'redirect_url' => null, 'checkout_id' => null];

        } catch (\Exception $e) {
            Log::error('STC Pay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'خطأ في الاتصال بـ STC Pay.', 'redirect_url' => null, 'checkout_id' => null];
        }
    }

    public function verify(PaymentTransaction $transaction, array $callbackData): bool
    {
        $otpRef  = $callbackData['OtpReference'] ?? $callbackData['otp_ref'] ?? null;
        $otpVal  = $callbackData['OtpValue'] ?? $callbackData['otp'] ?? null;

        if (!$otpRef || !$otpVal) {
            $transaction->markFailed($callbackData);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'X-ClientCode'   => $this->config->merchant_id,
                'X-ClientSecret' => $this->config->secret_key,
            ])->post($this->baseUrl() . '/directPayment/confirm', [
                'MerchantID'   => $this->config->merchant_id,
                'BranchID'     => $this->config->settings['branch_id'] ?? '1',
                'TellerID'     => $this->config->settings['teller_id'] ?? '1',
                'DeviceID'     => $this->config->settings['device_id'] ?? '1',
                'RefNum'       => $transaction->reference,
                'OtpReference' => $otpRef,
                'OtpValue'     => $otpVal,
            ]);

            $body = $response->json();

            if (($body['StatusCode'] ?? '') === '0000') {
                $transaction->markPaid($body['STCPayRefNum'] ?? $otpRef, $body);
                return true;
            }

            $transaction->markFailed($body);
            return false;

        } catch (\Exception $e) {
            Log::error('STC Pay verify exception', ['error' => $e->getMessage()]);
            $transaction->markFailed(['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool
    {
        try {
            $response = Http::withHeaders([
                'X-ClientCode'   => $this->config->merchant_id,
                'X-ClientSecret' => $this->config->secret_key,
            ])->post($this->baseUrl() . '/directPayment/reverse', [
                'MerchantID'    => $this->config->merchant_id,
                'BranchID'      => $this->config->settings['branch_id'] ?? '1',
                'RefNum'        => $transaction->reference,
                'STCPayRefNum'  => $transaction->gateway_reference,
            ]);

            return ($response->json()['StatusCode'] ?? '') === '0000';
        } catch (\Exception $e) {
            Log::error('STC Pay refund exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
