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

A Service that sends data (Publish) and receives data (Consume).

```php

use Buzzi\Service;
use Buzzi\Events\Ecommerce;

const BUZZI_API_ID     = "<your-buzzi-api-id-here>";
const BUZZI_API_SECRET = "<your-buzzi-api-secret-here>";

// Init
$service = new Service(BUZZI_API_ID, BUZZI_API_SECRET);


// Send Event
$response = $service->send(Ecommerce::TEST, ["message" => "Hello, World", "timestamp" => date(DATE_ATOM)]);


```