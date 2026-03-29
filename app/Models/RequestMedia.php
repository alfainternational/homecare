<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMedia extends Model
{
    protected $fillable = ['request_id', 'uploaded_by', 'path', 'type'];

    public function request() { return $this->belongsTo(ServiceRequest::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->path); }
}
