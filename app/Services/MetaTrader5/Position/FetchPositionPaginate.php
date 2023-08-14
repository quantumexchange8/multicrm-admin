<?php

namespace App\Services\MetaTrader5\Position;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class FetchPositionPaginate extends BaseApi
{
    public function execute(UserModel $user, $offset, $total)
    {
        return Client::getPositionPaginate($user->meta_login, $offset, $total);
    }
}
