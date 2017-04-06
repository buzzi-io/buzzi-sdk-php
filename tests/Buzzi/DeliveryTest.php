<?php

use Buzzi\Delivery;
use GuzzleHttp\Psr7\Response;

class DeliveryTest extends PHPUnit_Framework_TestCase
{
    public function testFromResponse()
    {
		$mockResponseHeaders = [
			'x-buzzi-account-id'          => 'fake-account-uuid',
			'x-buzzi-account-display'     => 'Fake Account',
			'x-buzzi-consumer-id'         => 'fake-consumer-uuid',
			'x-buzzi-consumer-display'    => 'Fake Consumer',
			'x-buzzi-delivery-id'         => 'fake-delivery-uuid',
			'x-buzzi-event-id'            => 'fake-event-uuid',
			'x-buzzi-event-type'          => 'buzzi.ecommerce.test',
			'x-buzzi-event-version'       => 'v1.0',
			'x-buzzi-event-display'       => 'Test Event',
			'x-buzzi-producer-id'         => 'fake-producer-uuid',
			'x-buzzi-producer-display'    => 'Fake Publisher',
			'x-buzzi-integration-id'      => 'fake-producer-uuid',
			'x-buzzi-integration-display' => 'Fake Integration',
			'x-buzzi-receipt'             => 'json.web.token',
			'x-buzzi-var-test-1'          => 'FakeVarTest1Value',
			'x-buzzi-var-test-2'          => 'FakeVarTest2Value',
			'x-buzzi-var-test-3'          => 'FakeVarTest3Value'
		];

		$mockResponseBody = '{ "message": "Hello, World!" }';

		$mockResponse = new Response(200, $mockResponseHeaders, $mockResponseBody);

		$delivery = Delivery::fromResponse($mockResponse);

		$this->assertInstanceOf(Delivery::class, $delivery);
	}
}