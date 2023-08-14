<?php

namespace App\Services\MetaTrader5\Symbol;


use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchSymbolByPos extends BaseApi
{
    public function execute($pos)
    {
        return Client::getSymbolByPos($pos);
    }
}
