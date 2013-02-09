<?php
require __DIR__.'/../vendor/autoload.php';

$values = array(2, 4, 6, 8, 10, 13, 14, 15, 18, 20, 30, 14, 15, 10, 10, 9, 3, 1);

// Tumbling windows
$count_fn = new React\EEP\Stats\Count;
$tumbling_count = new React\EEP\Window\Tumbling($count_fn, count($values));
$sum_fn = new React\EEP\Stats\Sum;
$tumbling_sum = new React\EEP\Window\Tumbling($sum_fn, count($values));
$min_fn = new React\EEP\Stats\Min;
$tumbling_min = new React\EEP\Window\Tumbling($min_fn, count($values));
$max_fn = new React\EEP\Stats\Max;
$tumbling_max = new React\EEP\Window\Tumbling($max_fn, count($values));
$mean_fn = new React\EEP\Stats\Mean;
$tumbling_mean = new React\EEP\Window\Tumbling($mean_fn, count($values));
$stdevs_fn = new React\EEP\Stats\Stdev;
$tumbling_stdevs = new React\EEP\Window\Tumbling($stdevs_fn, count($values));
$vars_fn = new React\EEP\Stats\Variance;
$tumbling_vars = new React\EEP\Window\Tumbling($vars_fn, count($values));

// Register callbacks
$tumbling_count->on('emit', function($value) { echo "count:\t", $value, "\n";});
$tumbling_sum->on('emit', function($value) { echo "sum:\t", $value, "\n";});
$tumbling_min->on('emit', function($value) { echo "min:\t", $value, "\n";});
$tumbling_max->on('emit', function($value) { echo "max:\t", $value, "\n";});
$tumbling_mean->on('emit', function($value) { echo "mean:\t", $value, "\n";});
$tumbling_stdevs->on('emit', function($value) { echo "stdevs:\t", $value, "\n";});
$tumbling_vars->on('emit', function($value) { echo "vars:\t", $value, "\n";});

echo "\nIndividual tumbling windows\n\n";

// Pump data into the tumbling windows
foreach($values as $v) {
  $tumbling_count->enqueue($v);
  $tumbling_sum->enqueue($v);
  $tumbling_min->enqueue($v);
  $tumbling_max->enqueue($v);
  $tumbling_mean->enqueue($v);
  $tumbling_stdevs->enqueue($v);
  $tumbling_vars->enqueue($v);
}

// Alternatively, use a composite aggregate function
$stats = array(
  $count_fn, $sum_fn, $min_fn, $max_fn, $mean_fn, $stdevs_fn, $vars_fn
);
$headers = array("Count\t", "Sum\t", "Min\t", "Max\t", "Mean\t", "Stdev\t", "Vars\t");

$comp_fn = new React\EEP\Composite($stats);
$tumbling = new React\EEP\Window\Tumbling($comp_fn, count($values));
$tumbling->on('emit', function($value) use($headers) { 
  for($i = 0; $i < count($value); $i++) {
    echo $headers[$i], "\t", $value[$i], "\n";
  }
});

echo "\nComposite tumbling window\n\n";

// Pump data into the tumbling window
foreach($values as $v) {
  $tumbling->enqueue($v);
}

echo "\n";