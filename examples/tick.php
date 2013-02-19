<?php
require __DIR__.'/../vendor/autoload.php';
use React\EEP\Aggregator;

class TickDetector implements Aggregator {
  private $state, $data, $time;
  
  public function init() {
    $this->state = 0;
    $this->data = array();
    $this->time = null;
  }
  
  public function accumulate($v) {
    switch($this->state) {
      case 0:
        // Initial state. Always accept the value.
        $this->data[0] = $v->value;
        $this->time = $v->at;
        $this->state = 1;
        break;
      case 1:
        // Look for a drop. 
        if($v->value < $this->data[0]) {
          $this->data[1] = $v->value;
          $this->state = 2;
        } else {
          $this->reset($v);
        }
        break;
      case 2:
        // Look for an uptick to a higher than original price.
        if($v->value > $this->data[0]) {
          $this->data[2] = $v->value;
          $this->state = 3;
        } else {
          $this->reset($v);
        }
        break;
      case 3:
        // We're detecting a drop again, so we can look for any price.
        $this->data[3] = $v->value;
        $this->state = 4;
        break;
      case 4: 
        // We need a drop, if not, update value and wait.
        if($v->value < $this->data[3]) {
          // We got one!
          $this->state = 5;
        } 
        $this->data[3] = $v->value;
        break;
      case 5:
        // Just reset.
        $this->reset($v);
        break; 
    }
  }
  
  private function reset($v) {
    $this->state = 0;
    $this->accumulate($v);
  }
  
  public function compensate($v) {
    // We only care about our example sliding out.
    if($this->state > 0 && $v->at == $this->time && $v->value == $this->time) {
      // If we've expired, reset.
      $this->init();
    }
  }
  
  public function emit() {
    if($this->state == 5) {
      return $this->data;
    }
  }
}

$tick_fn = new TickDetector();
$cb = function($value) { 
  if($value) {
    list($start, $low, $high, $cur) = $value;
    printf("Value: %d Prev Gain: %d\n", $cur, $high - $low);
  }
};

echo "Test on known data\n\n";

// Pump a little data. 
$values = array(290, 290, 300, 300, 320, 280, 340, 340, 350, 300, 290, 300);
$win = new React\EEP\Window\Sliding($tick_fn, 7);
$win->on('emit', $cb);
foreach($values as $at => $value) {
  $win->enqueue(new React\EEP\Event\Temporal($value, $at));
}

echo "\nTest on rand data\n\n";

// Pump a lot of data.
$win = new React\EEP\Window\Sliding($tick_fn, 20);
$win->on('emit', $cb);
$start = microtime(true);
for($i = 0; $i < 10000; $i++) {
  $win->enqueue(new React\EEP\Event\Temporal(rand(250, 350), $at));
}