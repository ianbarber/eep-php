<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Count all the things!
 */
class Count implements Aggregator 
{
  private $n;
  public function init() {
    $this->n = 0;
  }
  
  public function accumulate($_) {
    $this->n += 1;
  }
  
  public function compensate($_) {
    $this->n -= 1;
  }
  
  public function emit() {
    return $this->n;
  }
}