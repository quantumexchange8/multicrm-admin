<?php

namespace App\Services\MetaTrader5\User;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class CheckPassword extends BaseApi
{
    public function execute(UserModel $user, $password, $type = "MAIN")
    {
        return Client::checkPassword($user->meta_login, $password, $type);
    }
}