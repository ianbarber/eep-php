<?php

namespace React\EEP;

/**
 * Monotemporal value wrapper. Tracks origin time of an event.
 * Value can be updated. 
 * Updating a value does not update the origin.
 */
class Temporal {
  public $at, $value;
  
  public function __construct($value, $instant) {
    $this->value = $value;
    $this->at = $instant;
  }
}