# Showcase of Jackalope with Jackrabbit file handle issue using Imagick extension

This repository showcase's found issue when using file handles provided by Jackalope/PHPCR/Jackrabbit 
(it's hard to tell who is at fault) when using them with Imagick.

## Setup
Firstly install dependencies with composer:
```shell
composer install
```
Then verify that your implementation of Jackrabbit is running locally with default configuration or add connection 
credentials in `.env.test.local` to match yours (`.env.test.local` overwrites env variables in `.env.test`).

Default credentials can be found in `.env.test` and connection method is in [UnitTester](tests/Support/UnitTester.php) - 
compare them with your setup if you are not sure what is considered "default".

This should be it - proceed to Usage.

## Usage
There are two Test suits created:
- [JackrabbitImagickIssueCest](tests/Unit/JackrabbitImagickIssueCest.php) - showcasing an issue and is throwing an error be design
- [JackrabbitImagickWorkaroundCest](tests/Unit/JackrabbitImagickWorkaroundCest.php) - made to show that issue has a workaround (proxy file handle) which might help with developing the solution (?)

To run tests you can use either Codeception command line:
```shell
php vendor/bin/codecept run tests/Unit/JackrabbitImagickIssueCest.php
php vendor/bin/codecept run tests/Unit/JackrabbitImagickWorkaroundCest.php
```
or prepared Makefile commands:
```shell
make test-issue
make test-workaround
```

## Additional

#### `stream_get_meta_data`
The only indicators I found between normal steam and stream from PHPCR session is that `stream_get_meta_data` 
returns empty array on stream from implementation (but starts returning populated results, again, when the same stream 
contents are copied into another file - no additional data is provided into the file). Even `fstat` works normally on 
"corrupted" stream. 
