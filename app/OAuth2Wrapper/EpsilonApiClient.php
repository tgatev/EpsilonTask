<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 5/19/2019
 * Time: 7:52 PM
 */

namespace App\OAuth2Wrapper;


class EpsilonApiClient
{

    protected $http_client;
    protected $secret;

    protected $client_id;

    protected $refresh_token;

    protected $access_token;

    protected $expires_in;

    protected $token_type;

    public function __construct(){
        $init_data = config('auth.epsilon.OAuth');
        dd($init_data);
    }

    private function getHttpClient(){

    }

}