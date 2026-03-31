<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference', 'gateway_code', 'gateway_reference',
        'user_id', 'payable_type', 'payable_id',
        'amount', 'currency', 'status',
        'redirect_url', 'checkout_id', 'gateway_response', 'paid_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference)) {
                $model->reference = 'PT-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markPaid(string $gatewayReference, array $response = []): void
    {
        $this->update([
            'status'            => 'paid',
            'gateway_reference' => $gatewayReference,
            'gateway_response'  => $response,
            'paid_at'           => now(),
        ]);
    }

    public function markFailed(array $response = []): void
    {
        $this->update([
            'status'           => 'failed',
            'gateway_response' => $response,
        ]);
    }
}
