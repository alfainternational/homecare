<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id', 'name', 'name_ar', 'description', 'description_ar',
        'price', 'stock', 'sku', 'brand', 'image', 'is_featured', 'is_active'
    ];
    protected $casts = ['price' => 'decimal:2', 'is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function category() { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function getImageUrlAttribute(): string {
        return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.png');
    }
    public function inStock(): bool { return $this->stock > 0; }
}
