# 2.0.0 (2017-08-16)

### Highlights

* Introduced Buzzi\Sdk object which is a registry for SDK services
* Introduced Buzzi\Support\SupportService service
* Introduced Buzzi\Publish\PublishService service
* Introduced Buzzi\Publish\ConsumeService service
* Refactored Buzzi\Delivery and renamed to Buzzi\Consume\Delivery

# 0.1.0 (2017-05-22)

### Breaking Changes

* Refactored Buzzi\Service constructor to accept an associative array of configuration options.
* Refactored Buzzi\Delivery $property array to a standard set of properties.

### Highlights

* Adds new Generic Buzzi event types.
* Buzzi\Delivery properties will be much more friendly for IDEs.

### Features

* Adds file upload functionality.
* Adds optional sandbox flag to options to utilize the new sandbox host.

# 0.0.2 (2017-05-22)

### Bug Fixes

* Removed method type hinting. This caused problems for older versions of PHP. 

# 0.0.1 (2017-04-26)

### Highlights

* Base functionality.
