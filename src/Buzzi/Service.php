<?php

namespace Buzzi;

class Service
{
    const API_DEFAULT_VERSION = 'v1.0';
    const API_DEFAULT_HOST    = 'https://core.buzzi.io';
    const API_SANDBOX_HOST    = 'https://sandbox-core.buzzi.io';
    const API_HOST_ENV_NAME   = 'BUZZI_API_HOST';
    const API_ID_ENV_NAME     = 'BUZZI_API_ID';
    const API_SECRET_ENV_NAME = 'BUZZI_API_SECRET';

    /**
     * @var \Buzzi\Http
     */
    protected $http;

    /**
    * @param array $config
    */
    public function __construct(array $config = [])
    {
        $config = array_merge(
            [
                'host'        => getenv(self::API_HOST_ENV_NAME),
                'auth_id'     => getenv(self::API_ID_ENV_NAME),
                'auth_secret' => getenv(self::API_SECRET_ENV_NAME)
            ],
            $config
        );

        if (empty($config['host'])) {
            $config['host'] = !empty($config['sandbox']) && $config['sandbox']
                ? self::API_SANDBOX_HOST
                : self::API_DEFAULT_HOST;
        }

        $this->http = new Http($config);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function isAuthorized()
    {
    	return $this->http->get('/authorized');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function ping()
    {
    	return $this->http->get('/ping');
    }

    /**
     * @param string $type
     * @param array $payload
     * @param string $version
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send($type, $payload, $version = self::API_DEFAULT_VERSION)
    {
    	return $this->http->post(sprintf('/event/%s/%s', $type, $version), $payload);
    }

    /**
     * Upload File(s)
     *
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upload($data)
    {
        return $this->http->upload('/files', $data);
    }

    /**
     * @return \Buzzi\Delivery|null
     */
    public function fetch()
    {
        $response = $this->http->get('/event');

        if ($response->getStatusCode() !== 204) {
            $deliveryData = $this->http->parseHeaders($response);
            $deliveryData['body'] = (string)$response->getBody();
            return new Delivery($deliveryData);
        }

        return null;
    }

    /**
     * @param \Buzzi\Delivery|string $receipt
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function remove($receipt)
    {
        if ($receipt instanceof Delivery) {
            $receipt = $receipt->getReceipt();
        }

        if (!$this->isJwt($receipt)) {
            throw new \Exception('Invalid Delivery Receipt');
        }

    	return $this->http->delete('/event', ['receipt' => $receipt]);
    }

    /**
     * Check if a string is a JSON Web Token.
     *
     * @param string $string
     * @return bool
     */
    protected function isJwt($string)
    {   
        return count(explode('.', $string)) === 3;
    }
}
