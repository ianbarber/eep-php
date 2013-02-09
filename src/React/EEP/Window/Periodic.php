<?php

namespace React\EEP\Window;

use React\EEP\Aggregator;
use React\EEP\Window;
use React\EEP\Clock\Wall;
use React\EEP\Temporal;
use Evenement\EventEmitter;

/**
 * A periodic window. Lowest granularity, or a moment of time, is milliseconds.
 * As wall clock time is used, this window is not monotonic. 
 * Here be dragons like NTP, PTP, basically.
 */
class Periodic extends Monotonic implements Window
{ 
  public function __construct(Aggregator $aggregator, $millis) {
    $this->clock = new Wall($millis);
    parent::__construct($aggregator, $this->clock);
  }
}