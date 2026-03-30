<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'plan_id', 'status', 'starts_at', 'ends_at', 'visits_used', 'visits_total', 'auto_renew'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'auto_renew' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }

    public function visitsRemaining(): int { return max(0, $this->visits_total - $this->visits_used); }
    public function getVisitsRemainingAttribute(): int { return $this->visitsRemaining(); }
    public function isActive(): bool { return $this->status === 'active' && $this->ends_at?->isFuture(); }
}
