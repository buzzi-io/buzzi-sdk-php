<?php

namespace Buzzi;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Buzzi\Utils\StringUtils;

class Http
{
    use StringUtils;

    const BUZZI_HEADER_PREFIX     = 'x-buzzi-';
    const BUZZI_VAR_HEADER_PREFIX = 'x-buzzi-var-';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->client = new Client([
            'base_uri' => $config['host'],
            RequestOptions::AUTH => [
                $config['auth_id'],
                $config['auth_secret']
            ],
            RequestOptions::HTTP_ERRORS => false
        ]);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     */
    public function get($uri, $params = [])
    {
        $options = empty($params) ? [] : [RequestOptions::QUERY => $params];
        return $this->client->get($uri, $options);
    }

    /**
     * @param string $uri
     * @param array $postData
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     */
    public function post($uri, $postData = [])
    {
        $options = empty($postData) ? [] : [RequestOptions::JSON => $postData];
        return $this->client->post($uri, $options);
    }

    /**
     * @param string $uri
     * @param array $multipart
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     */
    public function upload($uri, $multipart)
    {
        return $this->client->post($uri, [RequestOptions::MULTIPART => $multipart]);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     */
    public function delete($uri, $params = [])
    {
        $options = empty($params) ? [] : [RequestOptions::QUERY => $params];
        return $this->client->delete($uri, $options);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    public function parseHeaders($response)
    {
        $data = [];
        $variables = [];

        foreach ($response->getHeaders() as $name => $values) {
            if (strpos($name, self::BUZZI_HEADER_PREFIX) === false) {
                continue;
            }

            if (strpos($name, self::BUZZI_VAR_HEADER_PREFIX) === false) {
                $data[$this->kebabCaseToSnakeCase(str_replace(self::BUZZI_HEADER_PREFIX, '', $name))] = implode(', ', $values);
            } else {
                $variables[str_replace(self::BUZZI_VAR_HEADER_PREFIX, '', $name)] = implode(', ', $values);
            }
        }

        $data['variables'] = $variables;
        return $data;
    }
}
