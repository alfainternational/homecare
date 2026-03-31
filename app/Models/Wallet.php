<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];
    protected $casts    = ['balance' => 'decimal:2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Credit the wallet — atomic: lock row, increment, log transaction.
     */
    public function credit(float $amount, string $desc = ''): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $desc) {
            // Re-fetch with pessimistic lock to prevent race conditions
            $wallet = self::lockForUpdate()->findOrFail($this->id);
            $wallet->increment('balance', $amount);
            $this->balance = $wallet->balance; // sync in-memory

            return $this->transactions()->create([
                'type'        => 'credit',
                'amount'      => $amount,
                'description' => $desc,
                'balance_after' => $wallet->balance,
            ]);
        });
    }

    /**
     * Debit the wallet — atomic: check balance, lock row, decrement, log transaction.
     *
     * @throws \RuntimeException if insufficient balance
     */
    public function debit(float $amount, string $desc = ''): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $desc) {
            $wallet = self::lockForUpdate()->findOrFail($this->id);

            if ($wallet->balance < $amount) {
                throw new \RuntimeException(
                    "رصيد المحفظة غير كافٍ. المطلوب: {$amount} ر.س — المتاح: {$wallet->balance} ر.س"
                );
            }

            $wallet->decrement('balance', $amount);
            $this->balance = $wallet->balance;

            return $this->transactions()->create([
                'type'          => 'debit',
                'amount'        => $amount,
                'description'   => $desc,
                'balance_after' => $wallet->balance,
            ]);
        });
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
