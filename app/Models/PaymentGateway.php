<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class PaymentGateway extends Model
{
    protected $fillable = [
        'code', 'name_ar', 'name_en', 'icon', 'is_enabled', 'mode',
        'api_key', 'secret_key', 'merchant_id', 'entity_id',
        'settings', 'sort_order', 'min_amount', 'max_amount',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings'   => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    // ─── Encrypted fields ──────────────────────────────────────────────────────

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiKeyAttribute(): ?string
    {
        return $this->attributes['api_key']
            ? Crypt::decryptString($this->attributes['api_key'])
            : null;
    }

    public function setSecretKeyAttribute(?string $value): void
    {
        $this->attributes['secret_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getSecretKeyAttribute(): ?string
    {
        return $this->attributes['secret_key']
            ? Crypt::decryptString($this->attributes['secret_key'])
            : null;
    }

    public function setMerchantIdAttribute(?string $value): void
    {
        $this->attributes['merchant_id'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getMerchantIdAttribute(): ?string
    {
        return $this->attributes['merchant_id']
            ? Crypt::decryptString($this->attributes['merchant_id'])
            : null;
    }

    public function setEntityIdAttribute(?string $value): void
    {
        $this->attributes['entity_id'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getEntityIdAttribute(): ?string
    {
        return $this->attributes['entity_id']
            ? Crypt::decryptString($this->attributes['entity_id'])
            : null;
    }

    // ─── Static helpers ────────────────────────────────────────────────────────

    public static function enabled(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('payment_gateways_enabled', 600, function () {
            return self::where('is_enabled', true)->orderBy('sort_order')->get();
        });
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    protected static function boot(): void
    {
        parent::boot();
        $flush = fn () => Cache::forget('payment_gateways_enabled');
        static::saved($flush);
        static::deleted($flush);
    }

    public function isTestMode(): bool
    {
        return $this->mode === 'test';
    }
}
