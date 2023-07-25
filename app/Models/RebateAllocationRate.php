<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateAllocationRate extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'old' => 'decimal:2',
        'new' => 'decimal:2',
    ];

    public function rebateAllocation()
    {
        return $this->belongsTo(RebateAllocation::class, 'allocation_id', 'id');
    }
    public function symbolGroup()
    {
        return $this->belongsTo(SymbolGroup::class, 'symbol_group', 'id');
    }
}
