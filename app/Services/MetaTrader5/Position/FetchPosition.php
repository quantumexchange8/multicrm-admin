<?php

namespace App\Services\MetaTrader5\Position;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchPosition extends BaseApi
{
    public function execute($ticket, $symbol)
    {
        return Client::getPosition($ticket, $symbol);
    }
}
