<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get the biggest of the things
 */
class Max implements Aggregator 
{
  private $max;
  private $cmp;
  
  public function __construct($compare_fn = null) {
    $this->cmp = $compare_fn ? $compare_fn : function($a, $b) {
      return $a == $b ? 0 : ($a < $b ? -1 : 1);
    };
  }
  
  public function init() {
    $this->max = null;
  }
  
  public function accumulate($v) {
    $c = $this->cmp;
    if($v === null) {
      return;
    } else if($this->max === null) {
      $this->max = $v;
    } else if($c($v, $this->max) > 0) {
      $this->max = $v;
    }
  }
  
  public function compensate($v) {
    return;
  }
  
  public function emit() {
    return $this->max;
  }
}