<?php

namespace Buzzi;

class Delivery
{
    const BUZZI_HEADER_PREFIX     = 'x-buzzi-';
    const BUZZI_VAR_HEADER_PREFIX = 'x-buzzi-var-';

    /**
     * @properties
     */
    public $accountId          = null;
    public $accountDisplay     = null;
    public $consumerId         = null;
    public $consumerDisplay    = null;
    public $deliveryId         = null;
    public $eventId            = null;
    public $eventType          = null;
    public $eventVersion       = null;
    public $eventDisplay       = null;
    public $producerId         = null;
    public $producerDisplay    = null;
    public $integrationId      = null;
    public $integrationDisplay = null;
    public $receipt            = null;
    public $variables          = null;
    public $body               = null;

    /**
     * Construct
     *
     * @param  array $data A key-value pair: key: property name && value: property value.
     */
    public function __construct($data)
    {
        foreach($data as $key => $value)
        {
            $key = $this->snakeCaseToCamelCase($key);
            $this->$key = $value;
        }
    }

    /**
     * Construct new Delivery object from response.
     *
     * @param  array A key-value pair: key: header name && value: header value.
     * @return new \Buzzi\Delivery
     */
    public static function fromResponse($response)
    {
        // Init
        $data = [];

        // Iterate header, skip Buzzi vars & non Buzzi headers, and add to data.
        foreach($response->getHeaders() as $name => $values)
        {
            if(strpos($name, self::BUZZI_HEADER_PREFIX) !== false && strpos($name, self::BUZZI_VAR_HEADER_PREFIX) === false)
            {
                $data[self::kebabCaseToSnakeCase(str_replace(self::BUZZI_HEADER_PREFIX, '', $name))] = implode(', ', $values);
            }
        }

        // Parse Buzzi variable headers and add to data.
        $data['variables'] = self::getVariablesFromHeaders($response->getHeaders());

        // Add body tp data.
        $data['body'] = $response->getBody();

        // Build and return object.
        return new self($data);
    }

    /**
     * Parse variables from an array of headers.
     *
     * @param  array A key-value pair: key is the header name and value is header value.
     * @return array
     */
    private static function getVariablesFromHeaders($headers)
    {
        // Init
        $variable = [];

        foreach($headers as $name => $values)
        {
            if(strpos($name, self::BUZZI_VAR_HEADER_PREFIX) !== false)
            {
                // Remove the Buzzi var header prefix.
                $variable[str_replace(self::BUZZI_VAR_HEADER_PREFIX, '', $name)] = implode(', ', $values);
            }
        }

        return $variable;
    }

    /**
     * Convert kebab-case-strings to snake_case.
     *
     * @param  string $string
     * @return string
     */
    private static function kebabCaseToSnakeCase($string) 
    {
        return str_replace('-', '_', $string);
    }

    /**
     * Convert snake_case to camelCase.
     *
     * @param  string $string
     * @param  bool   $capitalizeFirstCharacter (optional)
     * @return string
     */
    private static function snakeCaseToCamelCase($string, $capitalizeFirstCharacter = false) 
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if( ! $capitalizeFirstCharacter)
        {
            $str = lcfirst($str);
        }

        return $str;
    }
}