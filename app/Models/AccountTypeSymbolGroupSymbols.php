<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTypeSymbolGroupSymbols extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function group()
    {
        return $this->belongsTo(AccountTypeSymbolGroup::class, 'symbol_group', 'id');
    }
}
