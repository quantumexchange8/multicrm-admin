<?php

namespace App\Services\MetaTrader5\Trade;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;

class FetchTradingAccounts extends BaseApi
{
    public function execute($meta_login)
    {
        return Client::getTradingAccounts($meta_login);
    }
}