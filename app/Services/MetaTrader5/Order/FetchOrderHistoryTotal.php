<?php

namespace App\Services\MetaTrader5\Order;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchOrderHistoryTotal extends BaseApi
{
    public function execute(UserModel $user, $from, $to)
    {
        return Client::getOrderHistoryTotal($user->meta_login, $from, $to);
    }
}
