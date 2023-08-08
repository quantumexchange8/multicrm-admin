<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingAccountRebateRevenue extends Model
{
    use HasFactory;
    protected $table = 'trading_account_rebate_revenue';

    protected $fillable = ['status'];

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ofTicketUser()
    {
        return $this->belongsTo(User::class, 'ticket_user_id', 'id');
    }
    public function ofAccountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'id');
    }
}
