<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'request_number', 'client_id', 'technician_id', 'subscription_id',
        'service_type', 'status', 'description', 'voice_note_path',
        'priority', 'scheduled_at', 'completed_at', 'client_notes', 'admin_notes'
    ];
    protected $casts = ['scheduled_at' => 'datetime', 'completed_at' => 'datetime'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->request_number = 'WC-' . strtoupper(uniqid());
        });
    }

    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function technician() { return $this->belongsTo(User::class, 'technician_id'); }
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function reports() { return $this->hasMany(RequestReport::class, 'request_id'); }
    public function initialReport() { return $this->hasOne(RequestReport::class, 'request_id')->where('type', 'initial'); }
    public function finalReport() { return $this->hasOne(RequestReport::class, 'request_id')->where('type', 'final'); }
    public function media() { return $this->hasMany(RequestMedia::class, 'request_id'); }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'assigned' => 'تم التعيين',
            'on_way' => 'في الطريق',
            'arrived' => 'وصل الفني',
            'in_progress' => 'جاري التنفيذ',
            'awaiting_approval' => 'في انتظار موافقتك',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function getServiceTypeLabelAttribute(): string {
        return match($this->service_type) {
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'general' => 'صيانة عامة',
            default => $this->service_type,
        };
    }
}
