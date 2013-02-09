<?php

namespace React\EEP;

use React\EEP\Aggregator;

/**
 * Convenience function composite
 */
class Composite implements Aggregator 
{
  private $fns;
  
  public function __construct($fns) {
    $this->fns = $fns;
  }
  
  public function init() {
    foreach($this->fns as $fn) {
      $fn->init();
    }
  }
  
  public function accumulate($v) {
    foreach($this->fns as $fn) {
      $fn->accumulate($v);
    }
  }
  
  public function compensate($v) {
    foreach($this->fns as $fn) {
      $fn->compensate($v);
    }
  }
  
  public function emit() {
    $return = array();
    foreach($this->fns as $fn) {
      $return[] = $fn->emit();
    }
    return $return;
  }
}