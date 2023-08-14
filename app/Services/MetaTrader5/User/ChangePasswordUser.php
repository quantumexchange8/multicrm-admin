<?php

namespace App\Services\MetaTrader5\User;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;

class ChangePasswordUser extends BaseApi
{
    public function execute($metaLogin, $newPassword, $type = "MAIN")
    {
        return Client::changePasswordUser($metaLogin, $newPassword, $type);
    }
}
