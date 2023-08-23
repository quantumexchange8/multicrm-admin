<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class IbAccountTypeSymbolGroupRate extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $ib_account_type_group_rate = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('ib_account_type_group_rate')
            ->logOnly(['ib_account_type', 'symbol_group', 'amount'])
            ->setDescriptionForEvent(function (string $eventName) use ($ib_account_type_group_rate) {
                return Auth::user()->first_name . " has {$eventName} ib_account_type_id of {$ib_account_type_group_rate->ib_account_type}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ibAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'ib_account_type', 'id');
    }
    public function symbolGroup()
    {
        return $this->belongsTo(SymbolGroup::class, 'symbol_group', 'id');
    }
}
