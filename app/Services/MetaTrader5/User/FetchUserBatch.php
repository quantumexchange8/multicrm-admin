<?php

namespace App\Services\MetaTrader5\User;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchUserBatch extends BaseApi
{
    public function execute($logins)
    {
        //array of logins
        return Client::getUserBatch($logins);
    }
}