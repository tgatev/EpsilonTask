<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 5/19/2019
 * Time: 7:52 PM
 */

namespace App\OAuth2Wrapper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use JsonSerializable;

class EpsilonApiClient implements JsonSerializable
{
    const EAPI_BASE_URL = "http://democloudlx.epsilontel.com/api/";
    const EAPI_ACCESS_TOKEN_URL = self::EAPI_BASE_URL.'oauth2/access-token';
    const EAPI_REFRESH_TOKEN_URL = self::EAPI_BASE_URL.'oauth2/refresh-token';

    const EAPI_ACCEPT_HEADER = "application/vnd.cloudlx.v1+json";
    const KEY_LIFE_LENGTH_REFRESH = 604800;
    const KEY_LIFE_LENGTH_ACCESS = 3600;
    const TOKENS_CACHE_KEY = 'EpsilonApiTokens';

    /**
     * @var Client
     */
    protected $http_client;

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @var string
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $grant_type;

    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var DateTime
     */
    protected $access_expires_in;

    /**
     * @var DateTime
     */
    protected $refresh_expires_in;

    /**
     * @var secret
     */
    protected $token_type;

    /**
     * * CreateApiClient Instance
     *
     * EpsilonApiClient constructor.
     * @param array $data
     */
    public function __construct( array $data = array()){

        if(Cache::has( self::TOKENS_CACHE_KEY ) ){
            $init_data = json_decode(Cache::get(  self::TOKENS_CACHE_KEY ), JSON_OBJECT_AS_ARRAY) ;
        }else{
            $init_data = config('auth.epsilon.OAuth');
        }

        foreach($this as $key=> $value){
            if($key == "http_client") continue;
            @$this->$key = $data[$key]?:$init_data[$key];
        }

        $this->initHttpClient();
        $this->refreshTokens();
    }

    private function initHttpClient(){
        $this->http_client = new Client([
            "base_uri" => self::EAPI_BASE_URL,
            "timeout" => 3.0,
        ]);
    }

    /**
     * Refresh tokens
     */
    public function refreshTokens(){
        if($this->isExpiredAccess() and !$this->isExpiredRefresh()){
            $this->requestTokens( self::EAPI_REFRESH_TOKEN_URL, [
                "grant_type" => $this->grant_type,
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "refresh_token" => $this->refresh_token
            ]);
        }else{
            $this->requestTokens( self::EAPI_ACCESS_TOKEN_URL, [
                "grant_type" => $this->grant_type,
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
            ] );
        }

        Cache::add( self::TOKENS_CACHE_KEY , json_encode($this) , self::KEY_LIFE_LENGTH_REFRESH );
    }

    /**
     * Make request to api for token
     * @param $uri
     * @param array $params
     */
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

    /**
     * check is refresh token expired
     * @return bool
     */
    public function isExpiredRefresh(){
        if(!isset($this->refresh_expires_in)){
            return true;
        }
        if($this->refresh_expires_in > now()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * check is access token expired
     * @return bool
     */
    public function isExpiredAccess(){
        if(!isset($this->access_expires_in )){
            return true;
        }
        if($this->access_expires_in > now()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Make authenticated request to epsilon api
     *
     * @param $method
     * @param $endpoint
     * @param array $params
     * @return mixed
     */
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

    /**
     * @inheritDoc
     */
    function jsonSerialize()
    {
       return [
           "EpsilonApiClient" => [
                    "client_secret" => $this->client_secret,
                    "client_id" => $this->client_id,
                    "grant_type" => $this->grant_type,
                    "refresh_token" => $this->refresh_token,
                    "access_token" => $this->access_token,
                    "access_expires_in" => $this->access_expires_in,
                    "refresh_expires_in" => $this->refresh_expires_in,
                    "token_type" => $this->token_type
           ]
       ];
    }


}