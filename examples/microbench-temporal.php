<?php
require __DIR__.'/../vendor/autoload.php';
use Evenement\EventEmitter;

class MicroBench extends EventEmitter {
  private $max, $n, $count;
  
  public function __construct($max) {
    $this->max = $max;
    $this->count = $this->n = 0;
  }
  
  public function run($spec, $items, $size, $fn, $cb) {
    $start = microtime(true);
    $this->n = $fn();
    $end = microtime(true);
    $delta = $end - $start;
    $rate = ($this->n / $delta) / 1e6;
    $this->emit('end', array($cb, $size, $spec, $delta, $rate));
  }
  
  public function runSuite($op, $size, $fn) {
    $this->n = 0; $this->count= 0;
    $win = new React\EEP\Window\Periodic($op, $size);
    $win->on('emit', function($value) { $this->count++; });
    $this->run('Inline Periodic', null, $size, function() use ($win, $size) {
      $ticks = floor($this->max / $size);
      $n = 0;
      do {
        $win->tick();
        $win->enqueue($n++);
      } while($this->count < $ticks);
      return $n;
    }, $fn);
  }
  
  public function suite($op, $size, $fn) {
    $context = $this->runSuite($op, $size, $fn);
    return array("context" => $context);
  }
}
echo 'Micro Benching Embedded Event Processing', "\n";

$millis = 5000;
$bench = new MicroBench($millis);
$bench->on('end', function($fn, $size, $spec, $secs, $rate) {
  $fn($size, $spec, $secs, $rate);
});

$ops = array(
  "noop" => new React\EEP\Util\Noop,
  "count" => new React\EEP\Stats\Count,
  "sum" => new React\EEP\Stats\Sum,
  "min" => new React\EEP\Stats\Min,
  "max" => new React\EEP\Stats\Max,
  "mean" => new React\EEP\Stats\Mean,
  "stdevs" => new React\EEP\Stats\Stdev,
  "vars" => new React\EEP\Stats\Variance,
);

$sizes = array(1000, 100, 10, 1);
echo "1000\t100\t10\t1\top\n";
foreach($ops as $name => $op) {
  $results = array();
  $record = function($size, $spec, $secs, $rate) use (&$results) {
    $results[] = array("size" => $size, 
                      "name" => $spec, 
                      "elapsed" => $secs, 
                      "rate" => $rate);
  };
  foreach($sizes as $size) {
    $bench->suite($op, $size, $record);
  }
  printf("%.2f\t%.2f\t%.2f\t%.2f\t%s\n", 
    $results[0]['rate'], $results[1]['rate'], 
    $results[2]['rate'], $results[3]['rate'],
    $name);
  // Verify that we have 4*5s window
  // echo time(), "\n";
}