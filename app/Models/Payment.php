<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'real_amount' => 'decimal:2',
        'delete' => 'boolean',
    ];

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function toAccount()
    {
        return $this->belongsTo(TradingAccount::class, 'to', 'meta_login');
    }
    public function fromAccount()
    {
        return $this->belongsTo(TradingAccount::class, 'from', 'meta_login');
    }}
