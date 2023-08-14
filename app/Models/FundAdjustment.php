<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
