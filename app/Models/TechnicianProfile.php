<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianProfile extends Model
{
    protected $fillable = ['user_id', 'specializations', 'rating_average', 'total_ratings', 'status', 'bio', 'experience_years'];
    protected $casts = ['specializations' => 'array', 'rating_average' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
    public function isAvailable(): bool { return $this->status === 'available'; }
}
