<?php

use Buzzi\Delivery;
use Buzzi\Service;
use Buzzi\Events\Ecommerce;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    const TEST_BUZZI_API_ID     = "000000b1-1111-2222-3333-000000000000";
    const TEST_BUZZI_API_SECRET = "1234567812345678123456781234567812345678123456781234567812345678";

    /**
     * @property \Buzzi\Service $defaultService - Constructed with environment variables.
     */
	protected $defaultService;

    /**
     * @property \Buzzi\Service $unauthorizedService - Constructed with invalid argument values.
     */
    protected $unauthorizedService;

    /**
     * @property \Buzzi\Service $manuallyConstructedService - Constructed with given arguments.
     */
    protected $manuallyConstructedService;

    protected function setUp()
    {
        $this->defaultService = new Service();
        $this->unauthorizedService = new Service('INVALID_ID', 'INVALID_SECRET');
        $this->manuallyConstructedService = new Service(self::TEST_BUZZI_API_ID, self::TEST_BUZZI_API_SECRET);
    }

    public function testPing()
    {
        $response = $this->defaultService->ping();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUnsuccessfulAuthorization()
    {
        $this->setExpectedException(ClientException::class);

        $this->unauthorizedService->isAuthorized();
    }

    public function testSuccessfulAuthorizationViaDefaultServiceConstruction()
    {
        $response = $this->defaultService->isAuthorized();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSuccessfulAuthorizationViaManualServiceConstruction()
    {
        $response = $this->manuallyConstructedService->isAuthorized();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @depends testSuccessfulAuthorizationViaManualServiceConstruction
     */
    public function testSend()
    {
        $response = $this->defaultService->send(Ecommerce::TEST, ["message" => "Hello, World!", "timestamp" => date(DATE_ATOM)]);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @depends testSuccessfulAuthorizationViaManualServiceConstruction
     */
    public function testFetch()
    {
        $delivery = $this->defaultService->fetch();

        $this->assertInstanceOf(Delivery::class, $delivery);
    }

    /**
     * @depends testSuccessfulAuthorizationViaManualServiceConstruction
     */
    public function testRemove()
    {
        $sendResponse = $this->defaultService->send(Ecommerce::TEST, ["message" => "Hello, World!", "timestamp" => date(DATE_ATOM)]);

        $this->assertInstanceOf(Response::class, $sendResponse);

        $this->assertEquals(200, $sendResponse->getStatusCode());

        $delivery = $this->defaultService->fetch();

        $this->assertInstanceOf(Delivery::class, $delivery);

        $this->assertNotNull($delivery->receipt);

        $removeResponse = $this->defaultService->remove($delivery);

        $this->assertEquals(200, $removeResponse->getStatusCode());
    }
}