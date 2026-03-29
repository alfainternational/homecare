<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_number','user_id','service_request_id','status',
        'subtotal','tax','shipping','discount','total',
        'payment_method','payment_status','address','notes'
    ];
    protected $casts = [
        'subtotal'=>'decimal:2','tax'=>'decimal:2',
        'shipping'=>'decimal:2','discount'=>'decimal:2',
        'total'=>'decimal:2','address'=>'array'
    ];
    protected static function boot(){
        parent::boot();
        static::creating(fn($m)=>$m->order_number='ORD-'.strtoupper(uniqid()));
    }
    public function user(){return $this->belongsTo(User::class);}
    public function items(){return $this->hasMany(OrderItem::class);}
    public function serviceRequest(){return $this->belongsTo(ServiceRequest::class);}
}
