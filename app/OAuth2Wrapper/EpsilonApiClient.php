<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 5/19/2019
 * Time: 7:52 PM
 */

namespace App\OAuth2Wrapper;
use GuzzleHttp\Client;

class EpsilonApiClient
{
    const EAPI_BASE_URL = "http://democloudlx.epsilontel.com/api/";
    const EAPI_ACCESS_TOKEN_URL = self::EAPI_BASE_URL.'oauth2/access-token';
    const EAPI_REFRESH_TOKEN_URL = self::EAPI_BASE_URL.'oauth2/refresh-token';

    const EAPI_ACCEPT_HEADER = "application/vnd.cloudlx.v1+json";
    protected $http_client;
    protected $secret;

    protected $client_id;

    protected $refresh_token;

    protected $access_token;

    protected $access_expires_in;

    protected $refresh_expires_in;

    protected $token_type;

    public function __construct(){
        $init_data = config('auth.epsilon.OAuth');
        $this->secret = $init_data["secret"];
        $this->client_id = $init_data["client_id"];
        $this->grant_type = $init_data["grant_type"];
        $this->initHttpClient();
//        $this->getAccessToken();
        $this->refreshTokens();
        dd($this->request("GET" ,"services"));
    }

    private function initHttpClient(){
        $this->http_client = new Client([
            "base_uri" => self::EAPI_BASE_URL,
            "timeout" => 3.0,
        ]);
    }


    public function refreshTokens(){
        if(isset($this->refresh_expires_in) and $this->refresh_expires_in > now()){
            $this->requestTokens( self::EAPI_REFRESH_TOKEN_URL, [
                "grant_type" => $this->grant_type,
                "client_id" => $this->client_id,
                "client_secret" => $this->secret,
                "refresh_token" => $this->refresh_token
            ]);
        }elseif(!isset($this->refresh_expires_in) || now() > $this->refresh_expires_in ) {
            $this->requestTokens( self::EAPI_ACCESS_TOKEN_URL, [
                "grant_type" => $this->grant_type,
                "client_id" => $this->client_id,
                "client_secret" => $this->secret,
            ] );
        }
    }

    public function requestTokens($uri, array $params){
        $responce = $this->http_client->request("POST", $uri, [
            "headers" =>[
                "Accept" => self::EAPI_ACCEPT_HEADER
            ],
            'form_params' => $params
        ] );

        $body = json_decode($responce->getBody());
        $this->access_token = $body->access_token;
        $this->token_type = $body->token_type;
        $this->access_expires_in = now()->add(new \DateInterval( sprintf("P0Y0DT0H0M%dS",$body->expires_in) )) ;
        $this->refresh_expires_in = now()->add(new \DateInterval("P0Y7D") ) ;
        $this->refresh_token = $body->refresh_token;
    }

    public function expired(){
        if(isset($this->expires_in )){
            return true;
        }

        if($this->expires_in > now()){
            return false;
        }else{
            return true;
        }
    }

    public function request($method, $endpoint,array $params = array()){
        $responce = $this->http_client->request($method, self::EAPI_BASE_URL.$endpoint, [
            "headers" =>[
                "Accept" => self::EAPI_ACCEPT_HEADER,
                "Authorization" => sprintf("Bearer %s", $this->access_token)
            ],
            'form_params' => $params
        ]);

        return json_decode($responce->getBody());
    }
}