<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_number', 'client_id', 'technician_id', 'subscription_id',
        'address_id', 'service_type', 'service_category_id',
        'status', 'description', 'voice_note_path',
        'priority', 'scheduled_at', 'completed_at', 'client_notes', 'admin_notes',
        'street', 'district', 'city', 'rating',
        'request_type', // 'subscription' | 'on_demand' | 'marketplace'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'rating'       => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                // Cryptographically strong, collision-resistant: WC-YYYYMMDD-XXXXXXXX
                $model->request_number = 'WC-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function client()         { return $this->belongsTo(User::class, 'client_id'); }
    public function technician()     { return $this->belongsTo(User::class, 'technician_id'); }
    public function subscription()   { return $this->belongsTo(Subscription::class); }
    public function address()        { return $this->belongsTo(Address::class); }
    public function serviceCategory(){ return $this->belongsTo(ServiceCategory::class); }
    public function reports()        { return $this->hasMany(RequestReport::class, 'request_id'); }
    public function initialReport()  { return $this->hasOne(RequestReport::class, 'request_id')->where('type', 'initial'); }
    public function finalReport()    { return $this->hasOne(RequestReport::class, 'request_id')->where('type', 'final'); }
    public function media()          { return $this->hasMany(RequestMedia::class, 'request_id'); }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'قيد الانتظار',
            'assigned'          => 'تم التعيين',
            'on_way'            => 'في الطريق',
            'arrived'           => 'وصل الفني',
            'in_progress'       => 'جاري التنفيذ',
            'awaiting_approval' => 'في انتظار موافقتك',
            'completed'         => 'مكتمل',
            'cancelled'         => 'ملغي',
            default             => $this->status,
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        if ($this->serviceCategory) {
            return $this->serviceCategory->name_ar;
        }

        return match($this->service_type) {
            'plumbing'   => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac'       => 'تكييف',
            'general'    => 'صيانة عامة',
            default      => $this->service_type ?? '—',
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }
}
