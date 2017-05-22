<?php

namespace Buzzi;

use Buzzi\Delivery;
use Buzzi\Http;

class Service
{
    const API_DEFAULT_VERSION = 'v1.0';
    const API_DEFAULT_HOST    = 'https://core.buzzi.io';
    const API_SANDBOX_HOST    = 'https://sandbox-core.buzzi.io';
    const API_HOST_ENV_NAME   = 'BUZZI_API_HOST';
    const API_ID_ENV_NAME     = 'BUZZI_API_ID';
    const API_SECRET_ENV_NAME = 'BUZZI_API_SECRET';

    /**
     * @property \Buzzi\Http $http
     */
    protected $http;

    /**
    * Construct the Buzzi Client.
    *
    * @param array $config
    */
    public function __construct($config = [])
    {
        $this->config = array_merge(
            [
                'host'        => null,
                'auth_id'     => null,
                'auth_secret' => null
            ],
            $config
        );
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
     * Upload File(s)
     *
     * @param  array $data
     * @return \GuzzleHttp\Psr7\Response
     */
    public function upload($data)
    {
        return $this->request('POST', '/files', ['multipart' => $data]);
    }

    /**
     * Fetch
     *  
     * @return \Buzzi\Delivery
     */
    public function fetch()
    {
        $response = $this->request('GET', '/event');

        if($response->getStatusCode() !== 204)
        {
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
    protected function request($method, $url, $config = [])
    {
        $http = $this->getHttp();

    	return $http->request($method, $url, $config);
    }

    /**
     * Check if a string is a JSON Web Token.
     *
     * @param  string $string
     * @return bool
     */
    protected function isJwt($string)
    {   
        return (count(explode(".", $string)) === 3);
    }

    /**
     * Set the Http Client object.
     * 
     * @param  Buzzi\Http $http
     * @return void
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    /**
     * @return GuzzleHttp\ClientInterface implementation
     */
    public function getHttp()
    {
        if(null === $this->http)
        {
            $this->http = $this->createDefaultHttp();
        }

        return $this->http;
    }

    /**
     * Create default Http instance.
     * 
     * @return Buzzi\Http
     */
    protected function createDefaultHttp()
    {
        // Use configured base path if provided.
        $options = $this->config;

        // Guzzle exceptions option.
        $options['exceptions'] = false;

        // If sandbox option exists and is true, utilize the sandbox host const.
        if(array_key_exists('sandbox', $options))
        {
            if($options['sandbox'])
            {
                $options['host'] = self::API_SANDBOX_HOST; 
            }
        }

        // Attempt to fallback to default base path if still empty.
        if(empty($options['host']))
        {
            $options['host'] = (getenv(self::API_HOST_ENV_NAME) ? getenv(self::API_HOST_ENV_NAME) : self::API_DEFAULT_HOST); 
        }

        // Attempt to fallback to default auth identifer if empty.
        if(empty($options['auth_id']))
        {
            $options['auth_id'] = (getenv(self::API_ID_ENV_NAME) ? getenv(self::API_ID_ENV_NAME) : null);
        }

        // Attempt to fallback to default auth secret if empty.
        if(empty($options['auth_secret']))
        {
            $options['auth_secret'] = (getenv(self::API_SECRET_ENV_NAME) ? getenv(self::API_SECRET_ENV_NAME) : null);
        }

        return new Http($options);
    }
}