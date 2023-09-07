<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasRoles, SoftDeletes, LogsActivity, Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['full_name'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'investor_password',
        'phone_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    public function createPassword(string $field, string $password): self
    {
        $this->{$field} = app('hash')->make($password);

        return $this;
    }

    public function getChildrenIds()
    {
        $users = User::query()->where('hierarchyList', 'like', '%-' . $this->id . '-%')
            ->where('status', 1)
            ->pluck('id')->toArray();

        return $users;
    }

    public function totalGroupDeposit($user_id)
    {
        $users = $this->getChildrenIds();
        $users[] = $user_id;

        $amount = Payment::query()
            ->whereIn('user_id', $users)
            ->where('type', '=', 'Deposit')
            ->where('status', '=', 'Successful')
            ->sum('amount');

        $formattedAmount = number_format($amount, 2, '.', ''); // Format to 2 decimal places with no thousands separator

        // If the amount is 0, set it to '0.00'
        if ($formattedAmount === '0') {
            $formattedAmount = '0.00';
        }

        return $formattedAmount;
    }

    public function totalGroupWithdrawal($user_id)
    {
        $users = $this->getChildrenIds();
        $users[] = $user_id;

        $amount = Payment::query()
            ->whereIn('user_id', $users)
            ->where('type', '=', 'Withdrawal')
            ->where('status', '=', 'Successful')
            ->sum('amount');

        $formattedAmount = number_format($amount, 2, '.', ''); // Format to 2 decimal places with no thousands separator

        // If the amount is 0, set it to '0.00'
        if ($formattedAmount === '0') {
            $formattedAmount = '0.00';
        }

        return $formattedAmount;
    }

    public function getIbUserIds()
    {
        $users = User::query()->where('role', 'ib')->where('hierarchyList', 'like', '%-' . $this->id . '-%')
            ->where('status', 1)
            ->pluck('id')->toArray();

        return $users;
    }

    public function getMemberUserIds()
    {
        $users = User::query()->where('role', 'member')->where('hierarchyList', 'like', '%-' . $this->id . '-%')
            ->where('status', 1)
            ->pluck('id')->toArray();

        return $users;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['first_name', 'email', 'password', 'phone', 'chinese_name', 'status', 'role', 'dob', 'country', 'referral', 'last_login_ip', 'cash_wallet', 'kyc_approval', 'kyc_approval_description', 'cash_wallet_id', 'referral_code', 'ib_id', 'upline_id', 'hierarchyList', 'ct_user_id'])
            ->setDescriptionForEvent(function (string $eventName) use ($user) {
                return Auth::user()->first_name . " has {$eventName} {$user->first_name}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tradingUsers()
    {
        return $this->hasMany(TradingUser::class, 'user_id', 'id');
    }
    public function tradingAccounts()
    {
        return $this->hasMany(TradingAccount::class, 'user_id', 'id');
    }
    public function ibAccountTypes()
    {
        return $this->hasMany(IbAccountType::class, 'user_id', 'id');
    }
    public function userIb()
    {
        return $this->hasOne(IbAccountType::class, 'user_id', 'id');
    }
    public function specificIbAccountTypes($account_type)
    {
        return $this->hasOne(IbAccountType::class, 'user_id', 'id')->where('account_type', $account_type);
    }
    public function ibAccountTypeSymbolGroupRates()
    {
        return $this->hasManyThrough(IbAccountTypeSymbolGroupRate::class, IbAccountType::class, 'user_id', 'ib_account_type', 'email', 'id');
    }
    public function children()
    {
        return $this->hasMany(User::class, 'referral', 'ib_id');
    }
    public function parent()
    {
        return $this->belongsTo(User::class, 'referral', 'ib_id');
    }
    public function downline()
    {
        return $this->hasMany(User::class, 'upline_id', 'id');
    }
    public function upline()
    {
        return $this->belongsTo(User::class, 'upline_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

    public function getAddressAttribute()
    {
        return $this->address_1 . ', ' . ($this->address_2 ? $this->address_2 . ', ' : '') . $this->postcode . ', ' . $this->country;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    ////https://stackoverflow.com/a/62297282
    public function scopewhereFullName($query, $name)
    {
        return $query->where(DB::raw("REPLACE(CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,'')),'  ',' ')"), 'like', '%' . $name . '%');
    }
}
