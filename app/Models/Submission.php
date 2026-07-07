<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    protected $fillable = [
        'submission_number',
        'user_id',
        'category_id',
        'submission_date',
        'amount',
        'description',
        'current_status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SubmissionAttachment::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
