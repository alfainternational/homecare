<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable=['user_id','label','street','district','city','building_number','floor','extra_notes','latitude','longitude','is_primary'];
    protected $casts=['is_primary'=>'boolean','latitude'=>'decimal:8','longitude'=>'decimal:8'];
    public function user(){return $this->belongsTo(User::class);}
    public function getFullAddressAttribute():string{
        return implode('، ',array_filter([$this->building_number,$this->street,$this->district,$this->city]));
    }
}
