<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_number', 'client_id', 'service_category_id', 'address_id',
        'winning_bid_id', 'title', 'description', 'media_paths',
        'budget_min', 'budget_max', 'status',
        'preferred_date', 'expires_at', 'assigned_at', 'completed_at',
    ];

    protected $casts = [
        'media_paths'    => 'array',
        'budget_min'     => 'decimal:2',
        'budget_max'     => 'decimal:2',
        'preferred_date' => 'datetime',
        'expires_at'     => 'datetime',
        'assigned_at'    => 'datetime',
        'completed_at'   => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->post_number)) {
                $model->post_number = 'JP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function bids()
    {
        return $this->hasMany(JobBid::class);
    }

    public function winningBid()
    {
        return $this->belongsTo(JobBid::class, 'winning_bid_id');
    }

    public function acceptedBid()
    {
        return $this->hasOne(JobBid::class)->where('status', 'accepted');
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'مفتوح',
            'in_review'   => 'قيد المراجعة',
            'assigned'    => 'تم الاختيار',
            'in_progress' => 'جاري التنفيذ',
            'completed'   => 'مكتمل',
            'cancelled'   => 'ملغي',
            default       => $this->status,
        };
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
