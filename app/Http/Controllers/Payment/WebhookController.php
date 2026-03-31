<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * Handle gateway webhooks (Tamara, Tabby, HyperPay).
     */
    public function handle(Request $request, string $gateway)
    {
        Log::info("Payment webhook received [{$gateway}]", $request->all());

        $reference = $request->input('merchant_reference')
            ?? $request->input('order_reference_id')
            ?? $request->input('RefNum')
            ?? null;

        if (!$reference) {
            return response()->json(['message' => 'No reference found'], 400);
        }

        try {
            $paid = $this->paymentService->verify($reference, $request->all());
            return response()->json(['success' => $paid]);
        } catch (\Exception $e) {
            Log::error("Webhook error [{$gateway}]", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error processing webhook'], 500);
        }
    }

    /**
     * Handle payment callback redirect (user comes back from gateway).
     */
    public function callback(Request $request, string $reference)
    {
        $transaction = PaymentTransaction::where('reference', $reference)->first();

        if (!$transaction) {
            return redirect()->route('store.index')->with('error', 'لم يتم العثور على المعاملة.');
        }

        // HyperPay: verify using id query param
        if ($request->filled('id') && !$transaction->isPaid()) {
            $this->paymentService->verify($reference, $request->all());
            $transaction->refresh();
        }

        if ($transaction->isPaid()) {
            $this->fulfillPayable($transaction);
            return redirect($this->successUrl($transaction))
                ->with('success', 'تمت عملية الدفع بنجاح!');
        }

        return redirect($this->failedUrl($transaction))
            ->with('error', 'فشلت عملية الدفع. يرجى المحاولة مجدداً.');
    }

    /**
     * Show HyperPay payment widget.
     */
    public function checkoutWidget(string $reference)
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('payment.widget', compact('transaction'));
    }

    /**
     * Mark the payable (Order, ServiceRequest, etc.) as paid.
     */
    private function fulfillPayable(PaymentTransaction $transaction): void
    {
        $payable = $transaction->payable;

        if (!$payable) {
            return;
        }

        match(true) {
            $payable instanceof \App\Models\Order => $payable->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
            ]),
            default => null,
        };
    }

    private function successUrl(PaymentTransaction $transaction): string
    {
        return $transaction->redirect_url
            . '?status=success&ref=' . $transaction->reference;
    }

    private function failedUrl(PaymentTransaction $transaction): string
    {
        return $transaction->redirect_url
            . '?status=failed&ref=' . $transaction->reference;
    }
}
