<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class ServiceCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'name_ar', 'name_en', 'icon', 'image',
        'description_ar', 'is_active', 'is_on_demand',
        'is_marketplace', 'is_subscription',
        'base_price', 'price_on_request', 'sort_order',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'is_on_demand'      => 'boolean',
        'is_marketplace'    => 'boolean',
        'is_subscription'   => 'boolean',
        'price_on_request'  => 'boolean',
        'base_price'        => 'decimal:2',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    // ─── Static helpers ────────────────────────────────────────────────────────

    /** Cached list of active categories for dropdowns. */
    public static function activeList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('service_categories_active', 3600, function () {
            return self::where('is_active', true)->orderBy('sort_order')->get();
        });
    }

    /** Flush cache when a category changes. */
    protected static function boot(): void
    {
        parent::boot();
        $flush = fn () => Cache::forget('service_categories_active');
        static::saved($flush);
        static::deleted($flush);
    }
}
