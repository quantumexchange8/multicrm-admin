<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateAllocation extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(RebateAllocationRequest::class, 'request_id', 'id');
    }

    public function allocationRates()
    {
        return $this->hasMany(RebateAllocationRate::class, 'allocation_id', 'id');
    }
    public function fromIbAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'from', 'id');
    }
    public function toIbAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'to', 'id');
    }

    public function toOfUser()
    {
        return $this->hasManyThrough(User::class, IbAccountType::class, 'to', 'user_id', 'id', 'id');
    }
}
