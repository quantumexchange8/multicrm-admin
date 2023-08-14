<?php

namespace App\Services\MetaTrader5\Symbol;


use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchTotalSymbol extends BaseApi
{
    public function execute()
    {
        return Client::getTotalSymbol();
    }
}
