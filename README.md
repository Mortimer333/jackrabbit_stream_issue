# Showcase of Jackalope with Jackrabbit file handle issue using Imagick extension

This repository showcase's found issue when using file handles provided by Jackalope/PHPCR/Jackrabbit 
(it's hard to tell who is at fault) when using them with Imagick.

## Usage
There are two Test suits created:
- [JackrabbitImagickIssueCest](tests/Unit/JackrabbitImagickIssueCest.php) - showcasing an issue and is throwing an error be design
- [JackrabbitImagickWorkaroundCest](tests/Unit/JackrabbitImagickWorkaroundCest.php) - made to show that issue can be workaround if proxy file handle is used

To run then you can use either Codeception command line:
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
returns empty array on stream from implementation (but starts working again when the same stream contents are 
copied into another file - no additional data is provided into the file). Even `fstat` works normally on "corrupted" 
stream. 
