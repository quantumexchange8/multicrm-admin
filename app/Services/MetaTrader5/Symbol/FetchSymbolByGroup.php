<?php

namespace App\Services\MetaTrader5\Symbol;


use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;

class FetchSymbolByGroup extends BaseApi
{
    public function execute($name, $group)
    {
        return Client::getSymbolByGroup($name, $group);
    }
}
