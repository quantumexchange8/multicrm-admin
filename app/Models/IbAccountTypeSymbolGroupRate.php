<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IbAccountTypeSymbolGroupRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function ibAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'ib_account_type', 'id');
    }
    public function symbolGroup()
    {
        return $this->belongsTo(SymbolGroup::class, 'symbol_group', 'id');
    }
}
