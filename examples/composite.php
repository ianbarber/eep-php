<?php
require __DIR__.'/../vendor/autoload.php';

use React\EEP\Aggregator;

/**
 * A custom aggregate that inline composes aggregate functions.
 */
class LeStatFunction implements Aggregator {
  private $count, $sum, $min, $max, $mean, $vars, $stdevs;
  public function __construct() {
    $this->count = new React\EEP\Stats\Count;
    $this->sum = new React\EEP\Stats\Sum;
    $this->min = new React\EEP\Stats\Min;
    $this->max = new React\EEP\Stats\Max;
    $this->mean = new React\EEP\Stats\Mean;
    $this->vars = new React\EEP\Stats\Variance;
    $this->stdevs = new React\EEP\Stats\Stdev;
  }
  public function init() {
    $this->count->init();
    $this->sum->init();
    $this->min->init();
    $this->max->init();
    $this->mean->init();
    $this->vars->init();
    $this->stdevs->init();
  }
  public function accumulate($v) {
    $this->count->accumulate($v);
    $this->sum->accumulate($v);
    $this->min->accumulate($v);
    $this->max->accumulate($v);
    $this->mean->accumulate($v);
    $this->vars->accumulate($v);
    $this->stdevs->accumulate($v);
  }
  public function compensate($v) {
    $this->count->compensate($v);
    $this->sum->compensate($v);
    $this->min->compensate($v);
    $this->max->compensate($v);
    $this->mean->compensate($v);
    $this->vars->compensate($v);
    $this->stdevs->compensate($v);
  }
  public function emit() {
    return array(
      "count" => $this->count->emit(),
      "sum" => $this->sum->emit(),
      "min" => $this->min->emit(),
      "max" => $this->max->emit(),
      "mean" => $this->mean->emit(),
      "vars" => $this->vars->emit(),
      "stdevs" => $this->stdevs->emit());
  }
}

$fns = array(
  "count" => new React\EEP\Stats\Count,
  "sum" => new React\EEP\Stats\Sum,
  "min" => new React\EEP\Stats\Min,
  "max" => new React\EEP\Stats\Max,
  "mean" => new React\EEP\Stats\Mean,
  "vars" => new React\EEP\Stats\Variance,
  "stdevs" => new React\EEP\Stats\Stdev,
);
$headers = array("Count\t\t", "Sum\t\t", "Min\t\t", "Max\t\t", "Mean\t\t", "Variance\t", "Stdev\t\t");

// Convenient. But should only be used for composing independant, not related functions
$comp_fn = new React\EEP\Composite($fns);
$clock = new React\EEP\Clock\Wall(0);
$m1 = new React\EEP\Window\Monotonic($comp_fn, $clock);

// Correct. The stats functions can be coalesced into a single function. 
// Faster by 4X
$fn = new React\EEP\Stats\All();
$clock = new React\EEP\Clock\Counting();
$m2 = new React\EEP\Window\Monotonic($fn, $clock);

// Neither. Faster than m1, but slower than m2. Save some cycles, use m2!
$clock = new React\EEP\Clock\Counting();
$fn = new LeStatFunction();
$m3 = new React\EEP\Window\Monotonic($fn, $clock);

$m1->on("emit", function($values) use ($headers) {
  foreach($values as $i => $v) {
    echo $headers[$i], "\t\t", $v, "\n";
  }
});

$m2->on("emit", function($values) {
  echo json_encode($values), "\n";
});

$m3->on("emit", function($values) {
  echo json_encode($values), "\n";
});

if($_SERVER['argc'] != 2) {
  echo "\nUsage: ", $_SERVER['argv'][0], " <items>\n";
  exit;
}

$items = $_SERVER['argv'][1];

$start = microtime(true);
for($i = 1; $i <= $items; $i++) {
  $m1->enqueue($i);
}
$m1->tick();
$end = microtime(true);
printf("V1. Elapsed: %.2f meps: %.3f\n", ($end-$start), $items/($end-$start)/1e6);

$start = microtime(true);
for($i = 1; $i <= $items; $i++) {
  $m2->enqueue($i);
}
$m2->tick();
$end = microtime(true);
printf("V2. Elapsed: %.2f meps: %.3f\n", ($end-$start), $items/($end-$start)/1e6);

$start = microtime(true);
for($i = 1; $i <= $items; $i++) {
  $m3->enqueue($i);
}
$m3->tick();
$end = microtime(true);
printf("V3. Elapsed: %.2f\n", ($end-$start));