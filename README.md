# PHP Wrapper for pdfinfo

A PHP wrapper class for `pdfinfo` which is part of the [Poppler](https://poppler.freedesktop.org/) PDF rendering library.

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/pdfinfo` or add `"pointybeard/pdfinfo": "~1.0.0"` to your `composer.json` file.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Requirements

This library that `pdfinfo` is installed.

## Usage

Here is a basic usage example:

```php
<?php

declare(strict_types=1);

include "vendor/autoload.php";

use pointybeard\PdfInfo\PdfInfo;

$info = new PdfInfo("test.pdf");

var_dump(
    $info,
    $info->toArray(),
    $info->toJson(),
    (string)$info,
    $info->{"Page size"},
    $info->dimensions(),
    $info->height(),
    $info->width()
);
```

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/pdfinfo/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/pdfinfo/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"PHP Wrapper for pdfinfo" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
