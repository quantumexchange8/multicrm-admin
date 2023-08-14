<?php

namespace App\Services\MetaTrader5\Trade;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchDealPaginate extends BaseApi
{
    public function execute(UserModel $user, $from, $to, $offset, $total)
    {
        return Client::getDealPaginate($user->meta_login, $from, $to, $offset, $total);
    }
}