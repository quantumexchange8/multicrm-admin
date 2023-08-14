<?php

namespace App\Services\MetaTrader5\Trade;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class ConductUserBalance extends BaseApi
{
    public function execute(UserModel $user, $type, $balance, $comment)
    {
        return Client::conductUserBalance($user->meta_login, $type, $balance, $comment);
    }
}