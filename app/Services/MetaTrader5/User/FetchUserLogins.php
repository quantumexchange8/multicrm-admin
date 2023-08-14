<?php

namespace App\Services\MetaTrader5\User;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchUserLogins extends BaseApi
{
    public function execute(UserModel $user)
    {
        return Client::getUserLogins($user->group);
    }
}