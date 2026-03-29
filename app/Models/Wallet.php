<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable=['user_id','balance'];
    protected $casts=['balance'=>'decimal:2'];
    public function user(){return $this->belongsTo(User::class);}
    public function transactions(){return $this->hasMany(WalletTransaction::class);}
    public function credit(float $amount,string $desc=''):WalletTransaction{
        $this->increment('balance',$amount);
        return $this->transactions()->create(['type'=>'credit','amount'=>$amount,'description'=>$desc]);
    }
    public function debit(float $amount,string $desc=''):WalletTransaction{
        $this->decrement('balance',$amount);
        return $this->transactions()->create(['type'=>'debit','amount'=>$amount,'description'=>$desc]);
    }
}
