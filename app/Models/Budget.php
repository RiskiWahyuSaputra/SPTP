<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'category_id',
        'period',
        'allocated_amount',
        'used_amount',
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'used_amount' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function remaining(): float
    {
        return $this->allocated_amount - $this->used_amount;
    }
}
