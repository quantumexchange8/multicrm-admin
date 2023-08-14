<?php

namespace App\Services\MetaTrader5\Trade;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchDeal extends BaseApi
{
    public function execute($ticket)
    {
        return Client::getDeal($ticket);
    }
}