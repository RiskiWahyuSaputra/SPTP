<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBalance extends Model
{
    protected $fillable = [
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }
}
