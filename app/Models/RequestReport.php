<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestReport extends Model
{
    protected $fillable = [
        'request_id', 'type', 'reported_by', 'problem_description',
        'severity', 'parts_needed', 'estimated_duration', 'estimated_cost',
        'work_done', 'recommendations', 'is_approved', 'approved_at',
    ];
    protected $casts = ['parts_needed' => 'array', 'is_approved' => 'boolean', 'approved_at' => 'datetime'];

    public function request() { return $this->belongsTo(ServiceRequest::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
}
