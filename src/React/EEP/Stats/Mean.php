<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get the average of the things
 */
class Mean implements Aggregator 
{
  private $count;
  private $diff;
  private $mean;
  
  public function init() {
    $this->count = $this->diff = $this->mean = 0;
  }
  
  public function accumulate($v) {
    $this->count += 1;
    $this->diff = $v - $this->mean;
    $this->mean += ($this->diff/$this->count);
  }
  
  public function compensate($v) {
    $this->count -= 1;
    $this->diff = $this->mean - $v;
    $this->mean += ($this->diff/$this->count);
  }
  
  public function emit() {
    return $this->mean;
  }
}