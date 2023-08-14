<?php

namespace App\Services\MetaTrader5\User;

use App\Repositories\UsersRepo;
use App\Models\User as UserModel;
use AleeDhillon\MetaFive\Entities\User;
use App\Services\MetaTrader5\BaseApi;
use AleeDhillon\MetaFive\Facades\Client;
class UpdateUser extends BaseApi
{
    public function execute(UserModel $user)
    {
        $metaUser = new User();
        $metaUser->setName($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name);
        $metaUser->setEmail($user->email);
        $metaUser->setGroup($user->group);
        $metaUser->setLeverage($user->leverage);
        $metaUser->setPhone($user->phone);
        $metaUser->setAddress($user->address_1 . ' ' . $user->address_2);
        // $metaUser->setCity($user->city);
        // $metaUser->setState($user->state);
        $metaUser->setCountry($user->country);
        $metaUser->setZipCode($user->postcode);
        $metaUser->setMainPassword($user->password);
        $metaUser->setInvestorPassword($user->password);
        $metaUser->setPhonePassword($user->password);

        $newUser = Client::updateUser($metaUser);
        (new UsersRepo)->updateMetaLogin($user, $metaUser->getLogin());

        return $newUser;
    }
}