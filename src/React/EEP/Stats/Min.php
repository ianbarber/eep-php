<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get the smallest of the things
 */
class Min implements Aggregator 
{
  private $min;
  private $cmp;
  
  public function __construct($compare_fn = null) {
    $this->cmp = $compare_fn ? $compare_fn : function($a, $b) {
      return $a == $b ? 0 : ($a < $b ? -1 : 1);
    };
  }
  
  public function init() {
    $this->min = null;
  }
  
  public function accumulate($v) {
    $c = $this->cmp;
    if($v === null) {
      return;
    } else if($this->min === null) {
      $this->min = $v;
    } else if($c($v, $this->min) < 0) {
      $this->min = $v;
    }
  }
  
  public function compensate($v) {
    return;
  }
  
  public function emit() {
    return $this->min;
  }
  
  private function default_comparator($a, $b) {
    return $a < $b;
  }
}