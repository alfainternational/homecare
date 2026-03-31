<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'type', 'street', 'address_number',
        'district', 'city', 'building_number', 'floor',
        'extra_notes', 'latitude', 'longitude', 'map_place_id',
        'is_primary', 'max_visits_per_month',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match($this->type ?? 'home') {
            'home'       => 'منزل',
            'office'     => 'مكتب',
            'rest_house' => 'استراحة',
            default      => $this->type,
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type ?? 'home') {
            'home'       => '🏠',
            'office'     => '🏢',
            'rest_house' => '🏕️',
            default      => '📍',
        };
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->street,
            $this->address_number ? "رقم {$this->address_number}" : null,
            $this->district,
            $this->city,
        ])->filter()->implode('، ');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // ─── Methods ───────────────────────────────────────────────────────────────

    /**
     * Make this address the primary and unset all others for this user.
     */
    public function makePrimary(): void
    {
        Address::where('user_id', $this->user_id)->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }
}
