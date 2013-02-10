<?php

namespace React\EEP\Util;

use React\EEP\Aggregator;

/**
 * A (degenerated) temporal aggregate function. Useful for temporal stream
 * operators
 */
class Temporal implements Aggregator 
{
  private $inner, $instant, $value;
  
  public function __construct($fn, $instant) {
    $this->inner = $fn;
    $this->instant = $instant;
  }
  
  public function init() { 
    $this->inner->init();
    // Slight difference to EEP.js here. Not triggering an emit here
    // as this value will never make it anywhere, it could confuse
    // emit-sensitive functions (mainly me, during testing).
    $this->value = new \React\EEP\Temporal(null,$this->instant);
  }
  
  public function accumulate($v) { 
    $this->value->value = $this->inner->accumulate($v->value);
  }
  
  public function compensate($_) { }
  
  public function emit() { 
    return new \React\EEP\Temporal($this->inner->emit(), $this->instant);
  }
  
  public function at() {
    return $this->value->at;
  }
  
  public function update($time) {
    $this->instant = $time;
  }
}