<?php
require __DIR__.'/../vendor/autoload.php';

function head($type, $name) {
  echo "Microbench - Window Type: ",  $type, " with function '", $name, "'\n";
  echo "Type, Function, Size, Elapsed, Meps\n";
}

function foot() {
  echo "\n";
}

function bench($type, $name, $win, $size, $items) {
  $count = $items;
  $start = microtime(true);
  do {
    $win->enqueue(1);
  } while(--$count > 0);
  $elapsed = microtime(true) - $start;
  $meps = ($items/$elapsed) / 10e6;
  printf("%s,%s,%d,%.3f,%.3f\n", $type, $name, $size, $elapsed, $meps);
}

$fns = array(
  "noop" => new React\EEP\Util\Noop,
  "count" => new React\EEP\Stats\Count,
  "sum" => new React\EEP\Stats\Sum,
  "min" => new React\EEP\Stats\Min,
  "max" => new React\EEP\Stats\Max,
  "mean" => new React\EEP\Stats\Mean,
  "vars" => new React\EEP\Stats\Variance,
  "stdevs" => new React\EEP\Stats\Stdev,
  // TODO: All
);

$sizes = array(2,4,8,16,32,64,128,256,512);
$items = 100000; // 100k :(

echo "Tumbling\n";
foreach($fns as $name => $fn) {
  head("Tumbling", $name);
  foreach($sizes as $size) {
    $tumbling = new React\EEP\Window\Tumbling($fn, $size);
    bench('tumbling', $name, $tumbling, $size, $items);
  }
  foot();
}

echo "Sliding\n";
foreach($fns as $name => $fn) {
  head("Sliding", $name);
  foreach($sizes as $size) {
    $tumbling = new React\EEP\Window\Sliding($fn, $size);
    bench('sliding', $name, $tumbling, $size, $items);
  }
  foot();
}