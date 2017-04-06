<?php

namespace Buzzi;

use GuzzleHttp\Client;

class Http
{
    /**
     * @property \GuzzleHttp\Client $client
     */
    private $client;

    /**
     * Construct
     *
     * @param string $baseUri
     * @param string $authUsername
     * @param string $authPassword
     */
    public function __construct($baseUri, $authUsername, $authPassword)
    {
    	$this->client = new Client([
            'base_uri' => $baseUri,
            'auth'     => [
                $authUsername,
                $authPassword
            ]
        ]);
    }

    /**
     *  Request
     *
     * @param  string $method
     * @param  string $uri
     * @param  array  $config
     * @return \GuzzleHttp\Psr7\Response
     */
    public function request(string $method, string $uri, array $config = [])
    {
        return $this->client->request($method, $uri, $config);
    }
}