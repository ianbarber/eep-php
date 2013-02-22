<?php
require __DIR__.'/../vendor/autoload.php';
use React\EEP\Aggregator;

/**
 * Hash based semijoin between two streams 
 */
class Semijoin implements Aggregator {
  private $stream, $last_value;
  public function __construct() {
    $this->stream = array();
  }
  public function init() {
    $this->stream[0] = array();
    $this->stream[1] = array();
    $this->last_value = null;
  }
  
  /**
   * Take a muxed tuple, and treat the first 
   * value as the join key.
   */
  public function accumulate($v) {
    $this->last_value = null;
    $key = $v->value[0];
    if(!isset($this->stream[$v->stream][$key])) {
      $this->stream[$v->stream][$key] = 0;
    }
    $this->stream[$v->stream][$key] += 1;
    
    if(isset($this->stream[$v->stream ^ 1][$key])) {
      $this->last_value = $v->value;
    }
  }
  
  public function compensate($v) {
    $key = $v->value[0];
    $this->stream[$v->stream][$key]--;
    if($this->stream[$v->stream][$key] == 0) {
      unset($this->stream[$v->stream][$key]);
    }
  }
  
  public function emit() {
    return $this->last_value;
  }
}

$fn = new Semijoin();
$a_win = new React\EEP\Window\Sliding($fn, 100);
$b_win = new React\EEP\Window\Sliding($fn, 100);

$counter = 0;
$match = function($match) use (&$counter) {
  if($match) {
    $counter++;
    printf("%s \n", $match[1]);
  }
};
$a_win->on("emit", $match);
$b_win->on("emit", $match);

$event_count = 50000;
$cats = array("toys", "electrical", "cars", "clothing", "homewares");
$start = microtime(true);
$i = 0;
for($i = 0; $i < $event_count; $i++) {
  // Key - e.g auction ID.
  $key = rand(1, 5000);
  
  // Randomly select a stream.
  if(rand(0, 1) == 0) {
    // Generate an list item event. (auction id, category)
    $value = array($key, $cats[array_rand($cats)]);
    $a_win->enqueue(new React\EEP\Event\Muxed($value, 0));
  } else {
    // Generate an end auction event. (action id, category, price)
    $value = array($key, $cats[array_rand($cats)], rand(10, 500));
    $b_win->enqueue(new React\EEP\Event\Muxed($value, 1));
  }
}
$time = microtime(true) - $start;

// printf("%.2feps - %d matches\n", $event_count/$time, $counter);