# buzzi-sdk-php
Buzzi SDK for PHP

## Install

The recommended way to install buzzi-io/buzzi-sdk-php is [through composer](http://getcomposer.org).

```bash
composer require buzzi-io/buzzi-sdk-php
```

## Usage

Don't forget to autoload:

```php
<?php
require 'vendor/autoload.php';
```

### Initialize Sdk object:

```php
use Buzzi\Sdk;

$sdk = new Sdk([
    Sdk::CONFIG_AUTH_ID => '<your-buzzi-api-id-here>',
    Sdk::CONFIG_AUTH_SECRET => '<your-buzzi-api-secret-here>',
    
    // Optional parameters:
    //
    // Sdk::CONFIG_HOST => <buzzi-host>, if is not set will be used either production or sandbox predefined hosts depends on Sdk::CONFIG_SANDBOX
    // Sdk::CONFIG_SANDBOX => false,
    // Sdk::CONFIG_DEBUG => false,
    // Sdk::CONFIG_LOG_FILE_NAME => \Buzzi\Http::DEFAULT_LOG_FILE_NAME,
    // Sdk::CONFIG_LOG_LINE_FORMAT => \Buzzi\Http::DEFAULT_LOG_LINE_FORMAT,
    // Sdk::CONFIG_LOG_MESSAGE_FORMAT => \Buzzi\Http::DEFAULT_LOG_MESSAGE_FORMAT
]);
```

### In order to verify credentials:
```php
$supportService = $sdk->getSupportService();

try {

    $isAuthorized = $supportService->isAuthorized();

} catch (\Buzzi\Exception\HttpException $e) {

    $isAuthorized = false;
}
```

### In order to send (publish) data:

```php
$publishService = $sdk->getPublishService();

try {

    $eventId = $publishService->send('buzzi.generic.test', ["message" => "Hello, World", "timestamp" => date(DATE_ATOM)]);
    
} catch (\Buzzi\Exception\HttpException $e) {

    // The exceptions is thrown if http request is not successful
    
} catch (\RuntimeException $e) {

    // The exceptions is thrown if response does not contain event ID
}
```

### In order to get count of deliveries:
```php
$consumeService = $sdk->getConsumeService();

try {

    $countDeliveries = $consumeService->getCount();
    
} catch (\Buzzi\Exception\HttpException $e) {

    // The exceptions is thrown if http request is not successful
    
} catch (\RuntimeException $e) {

    // The exceptions is thrown if response does not contain count value
}
```

### In order to fetch one delivery:
```php
$consumeService = $sdk->getConsumeService();

try {

    $delivery = $consumeService->fetch();
    
} catch (\Buzzi\Exception\HttpException $e) {

    // The exceptions is thrown if http request is not successful
}
```

### In order to fetch a batch of deliveries:
```php
// fetchs all deliveries but not more then 100
list($deliveries, $exceptions) = $consumeService->batchFetch(100);
```

### In order to confirm that delivery is processed:
```php
try {

    $consumeService->confirmDelivery($delivery);
    // OR
    $consumeService->confirm($delivery->getReceipt());

} catch (\Buzzi\Exception\HttpException $e) {

    // The exceptions is thrown if http request is not successful
}
```

### In order to submit error during delivery processing:
```php
try {

    // $errorData could contain any information about the error
    $errorData = [
        'message' => 'reason for error'
    ];
    $consumeService->submitError($delivery->getReceipt(), $errorData);

} catch (\Buzzi\Exception\HttpException $e) {

    // The exceptions is thrown if http request is not successful
}
```

#### Information which could be taken from Buzzi\Exception\HttpException:
```php
try {

// request

} catch (\Buzzi\Exception\HttpException $e) {
    // The exceptions is thrown if http request is not successful

    $e->getMessage();           // returns either grabbed message from response body or reason phrase
    $e->getStatusCode();        // returns http status code of the response
    $e->getResponse();          // returns \Psr\Http\Message\ResponseInterface|\GuzzleHttp\Psr7\Response
    $e->getResponseBody();      // returns decoded response body
    $e->getResponseBody(false); // returns response body as json string
}
```
