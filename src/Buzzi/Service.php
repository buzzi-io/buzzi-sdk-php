<?php

namespace Buzzi;

use Buzzi\Delivery;
use Buzzi\Http;

class Service
{
    const API_DEFAULT_VERSION = 'v1.0';
    const API_DEFAULT_HOST    = 'https://core.buzzi.io';

    const API_HOST_ENV_NAME   = 'BUZZI_API_HOST';
    const API_ID_ENV_NAME     = 'BUZZI_API_ID';
    const API_SECRET_ENV_NAME = 'BUZZI_API_SECRET';

    /**
     * @property string $id
     */
    private $id;

    /**
     * @property string $secret
     */
    private $secret;

    /**
     * @property string $host
     */
    private $host;

    /**
     * @property \Buzzi\Http $http
     */
    protected $http;

    /**
     * Construct
     */
    public function __construct(string $id = null, string $secret = null, string $host = null)
    {
        $this->setId($id);
        $this->setSecret($secret);
        $this->setHost($host);
        $this->http = new Http($this->host, $this->id, $this->secret);
    }

    /**
     * Is Authorized
     *
     * @return \GuzzleHttp\Psr7\Response  
     */
    public function isAuthorized()
    {
    	return $this->request('GET', '/authorized');
    }

    /**
     * Ping
     *
     * @return \GuzzleHttp\Psr7\Response 
     */
    public function ping()
    {
    	return $this->request('GET', '/ping');
    }

    /**
     * Send
     *
     * @return \GuzzleHttp\Psr7\Response  
     */
    public function send($type, $payload, $version = self::API_DEFAULT_VERSION)
    {
    	return $this->request('POST', sprintf('/event/%s/%s', $type, $version), ['json' => $payload]);
    }

    /**
     * Fetch
     *  
     * @return \Buzzi\Delivery
     */
    public function fetch()
    {
        $response = $this->request('GET', '/event');

        if ($response->getStatusCode() !== 204) {
            return Delivery::fromResponse($response);
        }

        return null;
    }

    /**
     * Remove
     *  
     * @param  \Buzzi\Delivery $receipt
     * @param  array           $config
     * @return \GuzzleHttp\Psr7\Response
     */
    public function remove($receipt)
    {
        if($receipt instanceof Delivery)
        {
            $receipt = $receipt->receipt;
        }

        if( ! $this->isJwt($receipt))
        {
            throw new \Exception('Invalid Delivery Receipt');
        }

    	return $this->request('DELETE', sprintf('/event?receipt=%s', $receipt));
    }

    /**
     * Request
     *
     * @param string $method
     * @param string $url
     * @param array  $config
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function request(string $method, string $url, array $config = [])
    {
    	return $this->http->request($method, $url, $config);
    }

    /**
     * Set $id property - if null given check for env value.
     *
     * @param  string $id
     * @return void
     */
    protected function setId($id)
    {
        if(is_null($id))
        {
            $id = (empty(getenv(self::API_ID_ENV_NAME)) ? null : getenv(self::API_ID_ENV_NAME));
        }

        $this->id = $id;
    }

    /**
     * Set $secret property, if null is given check for env value.
     *
     * @param  string $secret
     * @return void
     */
    protected function setSecret($secret)
    {
        if(is_null($secret))
        {
            $secret = (empty(getenv(self::API_SECRET_ENV_NAME)) ? null : getenv(self::API_SECRET_ENV_NAME));
        }

        $this->secret = $secret;
    }

    /**
     * Set $host property, if null is given check for env value or utilize default.
     *
     * @param  string $host
     * @return void
     */
    protected function setHost($host)
    {
        if(is_null($host))
        {
            $host = (empty(getenv(self::API_HOST_ENV_NAME)) ? self::API_DEFAULT_HOST : getenv(self::API_HOST_ENV_NAME));
        }

        $this->host = $host;
    }

    /**
     * Check if a string is a JSON Web Token.
     *
     * @param  string $string
     * @return bool
     */
    protected function isJwt(string $string)
    {   
        return (count(explode(".", $string)) === 3);
    }
}