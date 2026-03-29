<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'name_ar', 'type', 'price', 'visits_per_year', 'features', 'is_featured', 'is_active'];
    protected $casts = ['features' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean', 'price' => 'decimal:2'];

    public function subscriptions() { return $this->hasMany(Subscription::class); }
}
