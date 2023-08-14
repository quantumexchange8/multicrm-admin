<?php

namespace App\Services\MetaTrader5\User;

use App\Repositories\UsersRepo;
use App\Models\User as UserModel;
use App\Services\Data\UpdateTradingAccount;
use App\Services\Data\UpdateTradingUser;
use AleeDhillon\MetaFive\Entities\User;
use App\Services\MetaTrader5\BaseApi;
use App\Services\MetaTrader5\Trade\FetchTradingAccounts;
use AleeDhillon\MetaFive\Facades\Client;

class UpdateRights extends BaseApi
{
    public function execute($meta_login, $rights)
    {

        /*  var_dump(dechex($rights));
        return; */
        $metaUser = new User();
        $metaUser->setLogin($meta_login);
        $metaUser->setRights($rights);

        $newUser = Client::updateUser($metaUser);
        (new UpdateTradingUser)->execute($meta_login, (new FetchUser)->execute($meta_login));
        return $newUser;
    }
}
