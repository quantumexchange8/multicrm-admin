<?php

namespace App\Services\MetaTrader5\Symbol;


use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;

class FetchSymbolByName extends BaseApi
{
    public function execute($name)
    {
        return Client::getSymbolByName($name);
    }
}
