<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianSubscription extends Model
{
    protected $fillable = [
        'technician_id', 'plan_type', 'amount_paid', 'status',
        'starts_at', 'ends_at', 'is_commission_model', 'commission_rate',
    ];

    protected $casts = [
        'starts_at'            => 'datetime',
        'ends_at'              => 'datetime',
        'amount_paid'          => 'decimal:2',
        'commission_rate'      => 'decimal:2',
        'is_commission_model'  => 'boolean',
    ];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }
}
