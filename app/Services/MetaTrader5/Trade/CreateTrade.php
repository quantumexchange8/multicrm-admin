<?php

namespace App\Services\MetaTrader5\Trade;

use App\Models\User as UserModel;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Entities\Trade;
use AleeDhillon\MetaFive\Facades\Client;
use App\Services\Data\UpdateTradingAccount;
use App\Services\Data\UpdateTradingUser;
use App\Services\MetaTrader5\User\FetchUser;

class CreateTrade extends BaseApi
{
    public function execute($meta_login, $amount, $comment, $type): Trade
    {
        $trade = new Trade();
        $trade->setLogin($meta_login);
        $trade->setAmount($amount);
        $trade->setComment($comment);
        $trade->setType($type);
        $trade = Client::trade($trade);
        (new UpdateTradingAccount)->execute($meta_login, (new FetchTradingAccounts)->execute($meta_login));
        (new UpdateTradingUser)->execute($meta_login, (new FetchUser)->execute($meta_login));
        return $trade;
    }
}
