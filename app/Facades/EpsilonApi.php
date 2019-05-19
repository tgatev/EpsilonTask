<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 5/19/2019
 * Time: 11:05 PM
 */

namespace App\Facades;


use App\OAuth2Wrapper\EpsilonApiClient;
use Illuminate\Support\Facades\Facade;
class EpsilonApi extends Facade
{

    protected static function getFacadeAccessor()
    {
       return EpsilonApiClient::class;
    }
}