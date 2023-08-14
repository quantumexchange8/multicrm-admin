<?php

namespace App\Services;

use AleeDhillon\MetaFive\Entities\Trade;
use AleeDhillon\MetaFive\Entities\User;
use AleeDhillon\MetaFive\Facades\Client;
use App\Models\TradingUser;
use App\Models\User as UserModel;
use App\Services\Auth\CreateTradingAccount;
use App\Services\Auth\CreateTradingUser;
use App\Services\Data\UpdateTradingAccount;
use App\Services\Data\UpdateTradingUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CTraderService

{
    private $host = "https://live-quantumcapital.webapi.ctrader.com";
    private $port = "8443";
    private $login = "10012";
    private $password = "Test1234.";
    private $baseURL = "https://live-quantumcapital.webapi.ctrader.com:8443";
    private $token = "6f0d6f97-3042-4389-9655-9bc321f3fc1e";
    private $brokerName = "quantumcapitalglobal";
    private $environmentName = "live";

    public function generateManagerToken()
    {
        $response =  Http::acceptJson()->post($this->baseURL . "/v2/webserv/managers/token", [
            'login' => $this->login,
            'hashedPassword' => md5($this->password),
        ])->json();
        Log::debug($response);
        // $response['webservToken'];
    }
    public function changePasswordUser($meta_login, $newPassword, $type = "MAIN")
    {
        $response = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders/{$meta_login}/changepassword?token={$this->token}", [
            'login' => $meta_login,
            'hashedPassword' => md5($newPassword),
        ]);
        Log::debug($response);
        if ($response->status() == 204) {
        }
    }

    public function changeTraderBonus($meta_login, $type, $amount)
    {
        $response = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders/{$meta_login}/changebonus?token={$this->token}", [
            'login' => $meta_login,
            'amount' => $amount * 100, //
            'preciseAmount' => $amount, //
            'type' => $type,
            'comment' => '', //
            'externalNote' => '', //
            'source' => '', //
            'externalId' => '', //
        ])->json();
        Log::debug($response);
        // $response['balanceHistoryId'];
    }

    public function listTraderGroups()
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/tradergroups?token={$this->token}")->json();
        Log::debug($response);
        //TraderGroupListTO
    }

    public function CreateWithdrawalRequest($meta_login, $amount)
    {
        $response = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders/{$meta_login}/withdrawRequest?token={$this->token}", [
            'login' => $meta_login, //
            'preciseAmount' => $amount, //
            'comment' => '', //
        ]);
        Log::debug($response);
        if ($response->status() == 204) {
        }
    }
    public function CreateCTID($email)
    {
        $response = Http::acceptJson()->post($this->baseURL . "/cid/ctid/create?token={$this->token}", [
            'brokerName' => $this->brokerName, //
            'email' => $email, //
            'preferredLanguage' => 'EN', //
        ])->json();
        Log::debug($response);
        /*  $response['userId'];
        $response['nickname'];
        $response['email'];
        $response['utcCreateTimestamp'];
        $response['status']; */
        return $response;
    }
    public function linkAccountTOCTID($meta_login, $password, $userId,)
    {
        $response = Http::acceptJson()->post($this->baseURL . "/cid/ctid/link?token={$this->token}", [
            'traderLogin' => $meta_login,
            'traderPasswordHash' => md5($password),
            'userId' => $userId,
            'brokerName' => $this->brokerName, //
            'environmentName' => $this->environmentName, //
            'returnAccountDetails' => false, //
        ])->json();
        Log::debug($response);
        //$response['ctidTraderAccountId'];
    }

    public function connectionStatus()
    {
        return [
            'code' => 0,
            'message' => "OK",
        ];
    }

    public function createUser(UserModel $user, $mainPassword, $investorPassword, $group, $leverage, $accountType, $leadCampaign = null, $leadSource = null, $remarks = null)
    {

        $accountResponse = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders?token={$this->token}", [
            'hashedPassword' => md5($mainPassword),
            'groupName' => $group,
            'depositCurrency' => 'USD',
            'name' => $user->full_name, //
            /*  'lastName' => '', //
             // */
            'description' => $remarks,
            'accessRights' => CTraderAccessRights::FULL_ACCESS,
            'balance' => 0,
            'leverageInCents' => $leverage * 100,
            'contactDetails' => [ //
                /*    'email' => '', //
                'address' => '', //
                'city' => '', //
                'state' => '', //
                'zipCode' => '', //
                'countryId' => 0, //
                'status' => '', //
                'documentId' => '', // */
                'phone' => $user->phone, //
                /*   'phonePassword' => '', // */
                // 'introducingBroker1' => '',
                // 'introducingBroker2' => '',
            ],
            'accountType' => CTraderAccountType::HEDGED,
            /*  'introducingBroker' => false, //
            'introducedByBroker' => 0, //
            'introducingBrokerCommissionRate' => 0, //
            'pocketCommissionRate' => 0, //
            'pocketMarkupRate' => 0, //
            'defaultIntroducingBrokerCommissionRate' => 0, //
            'defaultPocketCommissionRate' => 0, //
            'defaultPocketMarkupRate' => 0, //
            'defaultRebateRate' => 0, //
            'defaultSplitRevenue' => false, //
            'sendOwnStatement' => false, //false
            'limitedRisk' => false, //
            'swapFree' => false, //false
            'splitRevenue' => false, //
            'rankId' => 0, //
            'ranks' => [ //
                'id' => 0,
                'name' => '',
                'volume' => 0,
                'parentIbPercentage' => 0,
                'brokerPercentage' => 0,
            ],
            'brokerName' => 'quantumcapitalglobal', //
            'maxLeverage' => 0, //
            'frenchRisk' => false, // //deprecated
            'isLimitedRisk' => false, //
            'limitedRiskMarginCalculationStrategy' => TraderLimitedRiskMarginCalculationStrategy::ACCORDING_TO_LEVERAGE, //
            'sendStatementToBroker' => false, //
            'defaultIbCommissionsType' => CommissionType::USD_PER_LOT, //
            'ibCommissionsType' => CommissionType::USD_PER_LOT, // */
        ]);
        Log::debug($accountResponse);
        $accountResponse = $accountResponse->json();
        Log::debug($accountResponse);
        //TraderTO
        $response = $this->linkAccountTOCTID($accountResponse['login'], $mainPassword, $user->ct_user_id);
        Log::debug($response);
        (new CreateTradingUser)->execute($user, $accountResponse, $accountType, $remarks);
        (new CreateTradingAccount)->execute($user, $accountResponse, $accountType);
        return $accountResponse;
    }



    public function getUser($meta_login)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}")->json();
        //TraderTO
        Log::debug($response);
        return $response;
    }


    public function updateUser($meta_login, $data)
    {
        $response = Http::acceptJson()->put($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}", [
            'login' => $meta_login,
            'groupName' => $data->groupName,
            /*  'name' => '', //
            'lastName' => '', //
            'description' => '', //
            'accessRights' => CTraderAccessRights::FULL_ACCESS, //
            'leverageInCents' => 1, //
            'contactDetails' => [
                'email' => '', //
                'address' => '', //
                'city' => '', //
                'state' => '', //
                'zipCode' => '', //
                'countryId' => 0, //
                'status' => '', //
                'documentId' => '', //
                'phone' => '', //
                'phonePassword' => '', //
                // 'introducingBroker1' => '',
                // 'introducingBroker2' => '',
            ],
            'introducingBroker' => false, //
            'introducedByBroker' => 0, //
            'introducingBrokerCommissionRate' => 0, //
            'pocketCommissionRate' => 0, //
            'pocketMarkupRate' => 0, //
            'defaultIntroducingBrokerCommissionRate' => 0, //
            'defaultPocketCommissionRate' => 0, //
            'defaultPocketMarkupRate' => 0, //
            'defaultRebateRate' => 0, //
            'defaultSplitRevenue' => false, //
            'sendOwnStatement' => false, //false
            'limitedRisk' => false, //
            'swapFree' => false, //false
            'splitRevenue' => false, //
            'rankId' => 0, //
            'ranks' => [ //
                'id' => 0,
                'name' => '',
                'volume' => 0,
                'parentIbPercentage' => 0,
                'brokerPercentage' => 0,
            ],
            'brokerName' => 'quantumcapitalglobal', //
            'maxLeverage' => 0, //
            'frenchRisk' => false, // //deprecated
            'isLimitedRisk' => false, //
            'limitedRiskMarginCalculationStrategy' => TraderLimitedRiskMarginCalculationStrategy::ACCORDING_TO_LEVERAGE, //
            'sendStatementToBroker' => false, //
            'defaultIbCommissionsType' => CommissionType::USD_PER_LOT, //
            'ibCommissionsType' => CommissionType::USD_PER_LOT, // */
        ]);
        Log::debug($response);
        if ($response->status() == 204) {
        }
    }

    public function updateLeverage($meta_login, $leverage)
    {
        $tradingUser =  TradingUser::firstWhere('meta_login', $meta_login);
        $response = Http::acceptJson()->put($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}", [
            'login' => $meta_login,
            'groupName' => $tradingUser->meta_group,
            'leverageInCents' => $leverage * 100,

        ]);
        Log::debug($response);
        if ($response->status() == 204) {
            $data = $this->getUser($meta_login);
            (new UpdateTradingUser)->execute($meta_login, $data);
            (new UpdateTradingAccount)->execute($meta_login, $data);
        }
    }

    public function updateRights($meta_login, $rights)
    {

        /*  var_dump(dechex($rights));
        return; */
        $tradingUser =  TradingUser::firstWhere('meta_login', $meta_login);
        $response = Http::acceptJson()->put($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}", [
            'login' => $meta_login,
            'groupName' => $tradingUser->meta_group,
            'accessRights' => $rights,
        ]);
        Log::debug($response);
        if ($response->status() == 204) {
            $data = $this->getUser($meta_login);
            (new UpdateTradingUser)->execute($meta_login, $data);
        }
    }

    //changeTraderBalance
    public function createTrade($meta_login, $amount, $comment, $type): Trade
    {
        $response = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders/{$meta_login}/changebalance?token={$this->token}", [
            'login' => $meta_login,
            'amount' => $amount * 100, //
            'preciseAmount' => $amount, //
            'type' => $type,
            'comment' => $comment, //
            /* 'externalNote' => '', //
            'source' => '', //
            'externalId' => '', // */
        ]);
        Log::debug($response);
        $response = $response->json();
        Log::debug($response);
        $trade = new Trade();
        $trade->setAmount($amount);
        $trade->setComment($comment);
        $trade->setType($type);
        $trade->setTicket($response['balanceHistoryId']);


        $data = $this->getUser($meta_login);
        (new UpdateTradingUser)->execute($meta_login, $data);
        (new UpdateTradingAccount)->execute($meta_login, $data);
        return $trade;
    }

    public function getUserInfo($trading_users)
    {
        foreach ($trading_users as $row) {
            $data = $this->getUser($row->meta_login);
            (new UpdateTradingUser)->execute($row->meta_login, $data);
            (new UpdateTradingAccount)->execute($row->meta_login, $data);
        }
    }

    //=========================================================================================================
    //=========================================================================================================
    //                                             UNUSED
    //=========================================================================================================
    //=========================================================================================================

    public function deleteUser($meta_login)
    {
        $response = Http::acceptJson()->delete($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}");
        if ($response->status() == 204) {
        }
    }

    public function getAllUsers($from, $to, $groupId)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/traders?token={$this->token}", [
            'from' =>  Carbon::parse($from)->format("Y-m-dTH:i:s.v"),
            'to' =>  Carbon::parse($to)->format("Y-m-dTH:i:s.v"),
            'groupId' => $groupId,
        ])->json();
        //TraderListTO
    }
    public function getSubAccounts($meta_login)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/traders/{$meta_login}/subaccounts?token={$this->token}")->json();
        //SubAccountsTotalTO
    }

    public function listOpenPositions($meta_login = null)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/openPositions?token={$this->token}", ['login' => $meta_login])->json();
        //csv
    }

    public function listClosedPositions($from, $to, $meta_login = null)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/closedPositions?token={$this->token}", [
            'login' => $meta_login,
            'from' => Carbon::parse($from)->format("Y-m-dTH:i:s.v"),
            'to' =>  Carbon::parse($to)->format("Y-m-dTH:i:s.v"),
        ])->json();
        //csv
    }


    public function checkPassword($meta_login, $password, $type = "MAIN")
    {
        $response = Http::acceptJson()->post($this->baseURL . "/v2/webserv/traders/{$meta_login}/changepassword?token={$this->token}", [
            'login' => $meta_login,
            'hashedPassword' => md5($password),
        ]);
        if ($response->status() == 204) {
        }
    }

    public function changeWhiteLabel($meta_login, $brokerName, $environmentName)
    {
        $response = Http::acceptJson()->post($this->baseURL . "/cid/ctid/changeCtidTraderAccount?token={$this->token}", [
            'traderLogin' => $meta_login,
            'brokerName' => $brokerName, //
            'environmentName' => $environmentName, //
        ])->json();
        // $response['ctidTraderAccountId'];
    }
}


class CTraderAccessRights
{
    const FULL_ACCESS = "FULL_ACCESS";
    const CLOSE_ONLY = "CLOSE_ONLY";
    const NO_TRADING = "NO_TRADING";
    const NO_LOGIN = "NO_LOGIN";
}

class CTraderAccountType
{
    const HEDGED = "HEDGED";
    const NETTED = "NETTED";
}
class TraderTotalMarginCalculationType
{
    const MAX = "MAX";
    const SUM = "SUM";
    const NET = "NET";
}
class ChangeTraderBalanceType
{
    const DEPOSIT = "DEPOSIT";
    const DEPOSIT_NONWITHDRAWABLE_BONUS = "DEPOSIT_NONWITHDRAWABLE_BONUS";
    const WITHDRAW = "WITHDRAW";
    const WITHDRAW_NONWITHDRAWABLE_BONUS = "WITHDRAW_NONWITHDRAWABLE_BONUS";
}

class TraderLimitedRiskMarginCalculationStrategy
{
    const ACCORDING_TO_LEVERAGE = "ACCORDING_TO_LEVERAGE";
    const ACCORDING_TO_GSL = "ACCORDING_TO_GSL";
    const ACCORDING_TO_GSL_AND_LEVERAGE = "ACCORDING_TO_GSL_AND_LEVERAGE";
}
class CommissionType
{
    const USD_PER_MILLION_USD = "USD_PER_MILLION_USD";
    const USD_PER_LOT = "USD_PER_LOT";
}

class TraderTO
{
    public $token;
    public $groupName;
    public $depositCurrency;
    public $name = "";
    public $lastName = "";
    public $description = "";
    public $accessRights;
    public $balance;
    public $bonus;
    public $nonWithdrawableBonus;
    public $leverageInCents;
    public $contactDetails;
    public $registrationTimestamp;
    public $lastUpdateTimestamp;
    public $lastConnectionTimestamp = 0;
    public $equity;
    public $usedMargin;
    public $freeMargin;
    public $accountType;
    public $introducingBroker;
    public $introducedByBroker;
    public $introducingBrokerCommissionRate;
    public $pocketCommissionRate;
    public $pocketMarkupRate;
    public $defaultIntroducingBrokerCommissionRate;
    public $defaultPocketCommissionRate;
    public $defaultPocketMarkupRate;
    public $defaultRebateRate;
    public $defaultSplitRevenue;
    public $sendOwnStatement;
    public $limitedRisk;
    public $swapFree;
    public $splitRevenue;
    public $rankId;
    public $ranks;
    public $totalMarginCalculationType;
    public $brokerName;
    public $maxLeverage;
    public $frenchRisk;
    public $subAccountOf;
    public $isLimitedRisk;
    public $limitedRiskMarginCalculationStrategy;
    public $moneyDigits;
    public $sendStatementToBroker;
    public $defaultIbCommissionsType;
    public $ibCommissionsType;
}

class TraderContactDetailsTO
{
    public $email = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zipCode = '';
    public $countryId = 0;
    public $status = '';
    public $documentId = '';
    public $phone = '';
    public $phonePassword = '';
    public $introducingBroker1 = '';
    public $introducingBroker2 = '';
}
class TraderRankTO
{
    public $id;
    public $name;
    public $volume;
    public $parentIbPercentage;
    public $brokerPercentage;
}

class TraderListTO
{
    public $trader = array(); //TraderTO
    public $hasMore;
}
class SubAccountsTotalTO
{
    public $subAccountLogins; //SubAccountsTotalTOLogins
    public $totalSubAccountsBalance;
    public $totalSubAccountsEquity;
}

class SubAccountsTotalTOLogins
{
    public $login = array();
}
class CTraderErrorCodes
{
    const UNKNOWN_ERROR = "In case of server error.";
    const INVALID_REQUEST = "In case of client invalid request (request validation failure).";
    const NOT_ENOUGH_MONEY = "In case the trader doesn’t have enough money or has some open positions with unrealized profit/loss.";
    const CHANGE_BALANCE_BAD_AMOUNT = "In case the change balance amount is not valid. (e.g. amount < 0)";
    const TRADER_NOT_FOUND = "In case trader is not found.";
    const TRADER_GROUP_NOT_FOUND = "In case trader group not found on trader create/update operation.";
    const WRONG_PASSWORD = "In case trader’s password is wrong on \"Check Trader\'s Password\" operation";
    const NOT_ENOUGH_RIGHTS = "In case the client doesn’t have enough rights to perform the operation.";
    const REQUEST_FREQUENCY_EXCEEDED = "In case client sends more requests per hour than allowed. GET requests are not limited.";
}


class TraderGroupListTO
{
    public $traderGroup; //TraderGroupTO
}

class TraderGroupTO
{
    public $id;
    public $name;
    public $description;
}
