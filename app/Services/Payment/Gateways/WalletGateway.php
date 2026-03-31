<?php

namespace App\Services\Payment\Gateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Internal Wallet Gateway — deducts from the user's WarmConcierge wallet.
 */
class WalletGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly ?PaymentGateway $config) {}

    public function initiate(PaymentTransaction $transaction, array $extra = []): array
    {
        $user   = Auth::user();
        $wallet = $user?->wallet;

        if (!$wallet || !$wallet->hasSufficientBalance($transaction->amount)) {
            return [
                'success'      => false,
                'message'      => 'رصيد المحفظة غير كافٍ. الرصيد: ' . ($wallet?->balance ?? 0) . ' ر.س',
                'redirect_url' => null,
                'checkout_id'  => null,
            ];
        }

        // Wallet payment is instant — deduct immediately
        try {
            $wallet->debit(
                $transaction->amount,
                "دفع — {$transaction->reference}"
            );

            $transaction->markPaid('WALLET-' . $transaction->reference, [
                'wallet_id'  => $wallet->id,
                'balance_before' => $wallet->balance + $transaction->amount,
            ]);

            return [
                'success'      => true,
                'checkout_id'  => null,
                'redirect_url' => $transaction->redirect_url . '?status=success&ref=' . $transaction->reference,
                'message'      => 'تم الدفع من المحفظة بنجاح.',
            ];
        } catch (\RuntimeException $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'redirect_url' => null, 'checkout_id' => null];
        }
    }

    public function verify(PaymentTransaction $transaction, array $callbackData): bool
    {
        return $transaction->isPaid();
    }

    public function refund(PaymentTransaction $transaction, ?float $amount = null): bool
    {
        $user   = Auth::user() ?? $transaction->user;
        $wallet = $user->wallet;
        $refund = $amount ?? $transaction->amount;

        $wallet->credit($refund, "استرداد — {$transaction->reference}");
        $transaction->update(['status' => 'refunded']);
        return true;
    }
}
