<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RebateAllocationRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        $rebate_allocation_request = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('rebate_allocation_request_request')
            ->logOnly(['requestBy', 'handleBy', 'description', 'status'])
            ->setDescriptionForEvent(function (string $eventName) use ($rebate_allocation_request) {
                return Auth::user()->first_name . " has {$eventName} rebate_allocation_request_id of {$rebate_allocation_request->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function requestOfUser()
    {
        return $this->belongsTo(User::class, 'requestBy', 'id');
    }
    public function handleOfUser()
    {
        return $this->belongsTo(User::class, 'handleBy', 'id');
    }
    public function rebateAllocation()
    {
        return $this->hasMany(RebateAllocation::class, 'request_id', 'id');
    }
}
