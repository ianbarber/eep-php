# React/EEP

A port of Darach Ennis' Extreme Event Processing (eep.js - https://github.com/darach/eep-js) to PHP and React. Take a look at the README there for background and motivation. 

## Install

The recommended way to install react/eep is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "react/eep": "0.1.*"
    }
}
```

## Example

Here is an example of a sum tumbling window

```php
<?php
require __DIR__.'/../vendor/autoload.php';

$values = array(2, 4, 6, 8, 10, 13, 14, 15, 18, 20, 30, 14, 15, 10, 10, 9, 3);

$sum_fn = new React\EEP\Stats\Sum;
$tumbling_sum = new React\EEP\Window\Tumbling($sum_fn, count($values));

// Register callback
$tumbling_sum->on('emit', function($value) { echo "sum:\t", $value, "\n";});

// Pump data into the tumbling windows
foreach($values as $v) {
  $tumbling_sum->enqueue($v);
}

```

## Tests

To run the test suite, you need PHPUnit.

    $ phpunit

## License

See LICENSE.
