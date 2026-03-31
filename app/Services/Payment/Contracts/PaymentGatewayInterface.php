<?php

namespace App\Services\Payment\Contracts;

use App\Models\PaymentTransaction;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment and return the redirect URL or checkout data.
     *
     * @param  PaymentTransaction  $transaction
     * @param  array               $extra  Gateway-specific metadata
     * @return array{
     *   success: bool,
     *   redirect_url: string|null,
     *   checkout_id: string|null,
     *   message: string
     * }
     */
    public function initiate(PaymentTransaction $transaction, array $extra = []): array;

    /**
     * Verify/confirm a payment using the gateway's callback data.
     *
     * @param  PaymentTransaction  $transaction
     * @param  array               $callbackData
     * @return bool
     */
    public function verify(PaymentTransaction $transaction, array $callbackData): bool;

    /**
     * Process a refund.
     *
     * @param  PaymentTransaction  $transaction
     * @param  float|null          $amount  null = full refund
     */
    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool;
}
