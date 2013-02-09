<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Sum all the things
 */
class Sum implements Aggregator 
{
  private $total;
  
  public function init() {
    $this->total = 0;
  }
  
  public function accumulate($v) {
    $this->total += $v;
  }
  
  public function compensate($v) {
    $this->total -= $v;
  }
  
  public function emit() {
    return $this->total;
  }
}