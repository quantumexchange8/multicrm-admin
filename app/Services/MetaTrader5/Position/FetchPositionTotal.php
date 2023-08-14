<?php

namespace App\Services\MetaTrader5\Position;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchPositionTotal extends BaseApi
{
    public function execute(UserModel $user)
    {
        return Client::getPositionTotal($user->meta_login);
    }
}
