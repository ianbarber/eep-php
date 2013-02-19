<?php

namespace React\EEP\Event;

/**
 * Multiplexed stream event wrapped
 */
class Muxed {
  public $stream, $value;
  
  public function __construct($value, $stream) {
    $this->value = $value;
    $this->stream = $stream;
  }
}