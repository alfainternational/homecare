<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\HyperPayGateway;
use App\Services\Payment\Gateways\STCPayGateway;
use App\Services\Payment\Gateways\TabbyGateway;
use App\Services\Payment\Gateways\TamaraGateway;
use App\Services\Payment\Gateways\WalletGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Create a PaymentTransaction and initiate the payment.
     *
     * @param  string      $gatewayCode
     * @param  float       $amount
     * @param  object      $payable      Model instance (Order, ServiceRequest, etc.)
     * @param  string      $redirectUrl  URL to redirect after payment
     * @param  array       $extra
     * @return array{transaction: PaymentTransaction, result: array}
     */
    public function initiate(
        string $gatewayCode,
        float  $amount,
        object $payable,
        string $redirectUrl,
        array  $extra = []
    ): array {
        $gateway = PaymentGateway::findByCode($gatewayCode);

        if (!$gateway || !$gateway->is_enabled) {
            throw new \RuntimeException("طريقة الدفع '{$gatewayCode}' غير متاحة حالياً.");
        }

        if ($gateway->min_amount && $amount < $gateway->min_amount) {
            throw new \RuntimeException(
                "الحد الأدنى للدفع عبر {$gateway->name_ar} هو {$gateway->min_amount} ر.س"
            );
        }

        $transaction = DB::transaction(function () use ($gatewayCode, $amount, $payable, $redirectUrl) {
            return PaymentTransaction::create([
                'gateway_code'  => $gatewayCode,
                'user_id'       => Auth::id(),
                'payable_type'  => get_class($payable),
                'payable_id'    => $payable->id,
                'amount'        => $amount,
                'currency'      => 'SAR',
                'status'        => 'pending',
                'redirect_url'  => $redirectUrl,
            ]);
        });

        $driver = $this->resolveDriver($gatewayCode, $gateway);
        $result = $driver->initiate($transaction, $extra);

        if (!empty($result['checkout_id'])) {
            $transaction->update(['checkout_id' => $result['checkout_id']]);
        }

        return ['transaction' => $transaction, 'result' => $result];
    }

    /**
     * Verify a payment callback from the gateway.
     */
    public function verify(string $reference, array $callbackData): bool
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->lockForUpdate()
            ->firstOrFail();

        if ($transaction->isPaid()) {
            return true; // Already verified — idempotent
        }

        $gateway = PaymentGateway::findByCode($transaction->gateway_code);
        $driver  = $this->resolveDriver($transaction->gateway_code, $gateway);

        return $driver->verify($transaction, $callbackData);
    }

    /**
     * Map gateway code → driver implementation.
     */
    private function resolveDriver(string $code, ?PaymentGateway $gateway): PaymentGatewayInterface
    {
        return match($code) {
            'mada', 'apple_pay', 'credit_card' => new HyperPayGateway($gateway),
            'tabby'                             => new TabbyGateway($gateway),
            'tamara'                            => new TamaraGateway($gateway),
            'stc_pay'                           => new STCPayGateway($gateway),
            'wallet'                            => new WalletGateway($gateway),
            default => throw new \RuntimeException("لا يوجد driver للبوابة: {$code}"),
        };
    }
}
