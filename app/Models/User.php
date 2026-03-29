<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // Role helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTechnician(): bool { return $this->role === 'technician'; }
    public function isClient(): bool { return $this->role === 'client'; }
    public function isSupervisor(): bool { return $this->role === 'supervisor'; }

    // Relationships
    public function subscription() { return $this->hasOne(Subscription::class)->latestOfMany(); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class, 'client_id'); }
    public function assignedRequests() { return $this->hasMany(ServiceRequest::class, 'technician_id'); }
    public function technicianProfile() { return $this->hasOne(TechnicianProfile::class); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function addresses() { return $this->hasMany(Address::class); }
    public function primaryAddress() { return $this->hasOne(Address::class)->where('is_primary', true); }
    public function orders() { return $this->hasMany(Order::class); }
    public function referralsMade() { return $this->hasMany(Referral::class, 'referrer_id'); }
}
