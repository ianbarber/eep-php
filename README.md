# React/EEP

A port of Darach Ennis' Embedding Event Processing (eep.js - https://github.com/darach/eep-js) to PHP and React. Take a look at the README there for background and motivation. 

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

## Other Examples

The examples mainly come from Darach's, but also others from my talk on Event Stream Processing in PHP: 

1. analytics.php - example using the stats functions
2. composite.php - example combining stats functions
3. custom.php - Tim Bray's widefinder with a monotonic clock
4. leader.php - A beast of a custom window + agg that does lagged correlation for leader detection between two streams
5. lowrate.php - Simple periodic low rate detector example
6. microbench-nontemporal.php - Darach's event based benchmark
7. microbench-temporal.php - Darach's periodic benchmark
8. servermon.php - An example of using the All stats function to track several variables
9. tick.php - An aggreagate function which implements a simple state machine for pattern detection
10. tuplejoin.php - An auction themed example doing a hash based semijoin between two streams

## Tests

To run the test suite, you need PHPUnit.

    $ phpunit

## License

See LICENSE.
