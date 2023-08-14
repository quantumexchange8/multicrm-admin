<?php

namespace App\Services\MetaTrader5\Group;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchTotalGroup extends BaseApi
{
    public function execute()
    {
        return Client::getTotalGroup();
    }
}
