<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPayment extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'payment_type_id',
        'status',
        'screenshot_upload_id',
        'amount_xaf',
        'notes',
        'rejection_reason',
        'created_via',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'amount_xaf'   => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    // Status helpers
    public function isPending(): bool  { return $this->status === 'pending_review'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function isManualMark(): bool { return $this->created_via === 'manual_mark'; }

    // Relationships
    public function student()         { return $this->belongsTo(User::class, 'student_id'); }
    public function paymentType()     { return $this->belongsTo(PaymentType::class); }
    public function screenshot()      { return $this->belongsTo(Upload::class, 'screenshot_upload_id'); }
    public function reviewer()        { return $this->belongsTo(User::class, 'reviewed_by'); }
}