<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 5/19/2019
 * Time: 7:52 PM
 */

namespace App\OAuth2Wrapper;
use Faker\Provider\DateTime;
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
     * @var int
     */
    protected $expires_in;
    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var string
     */
    protected $access_expires_in;

    /**
     * @var string
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
        $init_data = array();
        if(Cache::has( self::TOKENS_CACHE_KEY ) ){
            $init_data = json_decode(Cache::get(  self::TOKENS_CACHE_KEY ), JSON_OBJECT_AS_ARRAY) ;
        }else{
            $init_data = config('auth.epsilon.OAuth');
        }

        foreach($this as $key=> $value){
            if($key == "http_client") continue;
            if(isset($data[$key])) {
                $this->$key = $data[$key];
            }else{
                $this->$key = isset($init_data[$key])? $init_data[$key] : null;
            }
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
        }elseif(!isset($this->access_token) and cache::has(self::TOKENS_CACHE_KEY)){
            $data = json_decode(Cache::get(self::TOKENS_CACHE_KEY));
            $this->setTokens( $data);
        }elseif($this->isExpiredRefresh()) {
            $this->requestTokens(self::EAPI_ACCESS_TOKEN_URL, [
                "grant_type" => $this->grant_type,
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
            ]);
        }
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
        $this->setTokens($body);
    }

    /**
     * Set tokens from cache or request
     * @param $data
     */
    private function setTokens($data){
        //cast to std object
        $data = (object)(array)$data;
        $this->access_token = $data->access_token;
        $this->token_type = $data->token_type;
        $this->expires_in = $data->expires_in?: self::KEY_LIFE_LENGTH_ACCESS;
        $this->access_expires_in = now()->add(new \DateInterval( sprintf("P0Y0DT0H0M%dS",$this->expires_in) ))->format("Y-m-d H:i:s") ;
        $this->refresh_expires_in = now()->add(new \DateInterval("P0Y7D"))->format("Y-m-d H:i:s") ;
        $this->refresh_token = $data->refresh_token;

        Cache::put( self::TOKENS_CACHE_KEY , json_encode($this) , self::KEY_LIFE_LENGTH_REFRESH );
    }
    /**
     * check is refresh token expired
     * @return bool
     */
    public function isExpiredRefresh(){
        if(!isset($this->refresh_expires_in)){
            return true;
        }
        $RefreshExpireDateTime = new \DateTime($this->getRefreshExpiresIn());
        $CurrentDateTime = now();

        if( $RefreshExpireDateTime > $CurrentDateTime){
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
        $AccessExpireDateTime = new \DateTime($this->getAccessExpiresIn());
        $CurrentDateTime = now();

        if($AccessExpireDateTime > $CurrentDateTime){
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
        // Refresh token when expired
        if($this->isExpiredAccess()) $this->refreshTokens();
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
            "client_secret" => $this->client_secret,
            "client_id" => $this->client_id,
            "expires_in" => $this->expires_in,
            "grant_type" => $this->grant_type,
            "refresh_token" => $this->refresh_token,
            "access_token" => $this->access_token,
            "access_expires_in" => $this->access_expires_in,
            "refresh_expires_in" => $this->refresh_expires_in,
            "token_type" => $this->token_type
       ];
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @return string
     */
    public function getGrantType()
    {
        return $this->grant_type;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return string
     */
    public function getAccessExpiresIn()
    {
        return $this->access_expires_in;
    }

    /**
     * @return string
     */
    public function getRefreshExpiresIn()
    {
        return $this->refresh_expires_in;
    }

    /**
     * @return secret
     */
    public function getTokenType()
    {
        return $this->token_type;
    }


}