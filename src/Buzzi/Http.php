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
     * Construct the Guzzle Client.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->client = new Client([
            'base_uri' => $config['host'],
            'auth'     => [
                $config['auth_id'],
                $config['auth_secret']
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
    public function request($method, $uri, $config = [])
    {
        return $this->client->request($method, $uri, $config);
    }
}