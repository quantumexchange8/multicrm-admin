<?php

namespace App\Services\MetaTrader5\Order;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class NewOrder extends BaseApi
{
    public function execute($ticket)
    {
        //https://github.com/tarikhagustia/php-mt5/blob/08912c0b09adf413b2354061cd61fc70402da613/src/MetaTraderClient.php#L740
        //https://github.com/tarikhagustia/laravel-mt5#open-order

        return false;
    }
}
