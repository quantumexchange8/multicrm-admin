<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTypeSymbolGroup extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = ['amount' => 'decimal:2'];

    public function symbols()
    {
        return $this->hasMany(AccountTypeSymbolGroupSymbols::class, 'symbol_group', 'id');
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'id');
    }
    public function symbolGroup()
    {
        return $this->belongsTo(SymbolGroup::class, 'symbol_group', 'id');
    }
}
