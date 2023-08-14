<?php

namespace App\Services\MetaTrader5\User;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;

class FetchUser extends BaseApi
{
    public function execute($meta_login)
    {
        return Client::getUser($meta_login);
    }
}