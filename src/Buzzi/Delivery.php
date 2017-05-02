<?php

namespace Buzzi;

class Delivery
{
	const BUZZI_HEADER_PREFIX     = 'x-buzzi-';
	const BUZZI_VAR_HEADER_PREFIX = 'x-buzzi-var-';

    /**
     * @property array $property
     */
    protected $property  = [
		'account_id'          => null,
		'account_display'     => null,
		'consumer_id'         => null,
		'consumer_display'    => null,
		'delivery_id'         => null,
		'event_id'            => null,
		'event_type'          => null,
		'event_version'       => null,
		'event_display'       => null, 
		'producer_id'         => null,
		'producer_display'    => null,
        'integration_id'      => null,
        'integration_display' => null,
		'receipt'             => null,
		'variables'           => null,
		'body'                => null
    ];

    /**
     * Construct
     *
     * @param  array $data A key-value pair: key: property name && value: property value.
     */
    public function __construct(array $data)
    {
    	foreach($data as $key => $value)
    	{
    		if(array_key_exists($key, $this->property))
    		{
    			$this->property[$key] = $value;
    		}
    	}
    }

    /**
     *  Overload magic getter to pull property value from an array.
     */
    public function __get($key)
    {
        return $this->property[$key];
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
    			$data[self::kebabToSnakeCase(str_replace(self::BUZZI_HEADER_PREFIX, '', $name))] = implode(', ', $values);
    		}
    	}

    	// Parse Buzzi variable headers and add to data.
    	$data['variables'] = self::getVariablesFromHeaders($response->getHeaders());

    	// Add body to data.
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
    private static function getVariablesFromHeaders(array $headers)
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
     * Convert kebab-case-strings to camelCase.
     *
     * @param  string $string
     * @param  bool   $capitalizeFirstCharacter (optional)
     * @return string
     */
	private static function kebabToCamelCase($string, $capitalizeFirstCharacter = false) 
	{
	    $str = str_replace('-', '', ucwords($string, '-'));

	    if( ! $capitalizeFirstCharacter)
	    {
	        $str = lcfirst($str);
	    }

	    return $str;
	}

    /**
     * Convert kebab-case-strings to snake-case.
     *
     * @param  string $string
     * @return string
     */
    private static function kebabToSnakeCase($string) 
    {
        return str_replace('-', '_', $string);
    }
}