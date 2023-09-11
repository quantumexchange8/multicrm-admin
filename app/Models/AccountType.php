<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountType extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'delete' => 'boolean',
        'minimum_deposit' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $account_type = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('account_type')
            ->logOnly(['name', 'group', 'minimum_deposit', 'leverage', 'currency', 'allow_create_account', 'type', 'commission_structure', 'trade_open_duration', 'show_register'])
            ->setDescriptionForEvent(function (string $eventName) use ($account_type) {
                return Auth::user()->first_name . " has {$eventName} Account Type of {$account_type->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function metaGroup()
    {
        return $this->belongsTo(Group::class, 'group', 'id');
    }
}
