<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobBid extends Model
{
    protected $fillable = [
        'job_post_id', 'technician_id', 'price', 'estimated_duration',
        'message', 'attachments', 'status', 'accepted_at',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'attachments'  => 'array',
        'accepted_at'  => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'بانتظار القرار',
            'accepted'  => 'مقبول',
            'rejected'  => 'مرفوض',
            'withdrawn' => 'سحب العرض',
            default     => $this->status,
        ];
    }
}
