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
class Periodic extends EventEmitter implements Window
{
  private $millis, $aggregator, $index;
  
  public function __construct(Aggregator $aggregator, $millis) {
    $this->millis = $millis;
    $this->clock = new Wall($millis);
    $this->clock->init();
    $this->aggregator = new \React\EEP\Util\Temporal($aggregator, $millis);
    $this->aggregator->init();
    $this->index = 0;
  }
  
  public function enqueue($event) {
    $this->aggregator->accumulate(new Temporal($event, $this->clock->inc()));
  }
  
  public function tick() {
    // If time hasn't passed, we're done
    if (!$this->clock->tick()) {
      return;
    } 
    
    // Otherwise, emit
    if ($this->clock->tock($this->aggregator->at())) {
      $this->emit('emit', array($this->aggregator->emit()->value));
      // 'close' current time window and 'open' a fresh one
      $this->aggregator->init();
    };
  }
}