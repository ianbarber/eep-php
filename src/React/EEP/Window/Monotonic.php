<?php

namespace React\EEP\Window;

use React\EEP\Aggregator;
use React\EEP\Clock;
use React\EEP\Window;
use React\EEP\Event\Temporal;
use Evenement\EventEmitter;

/**
 * A monotonic window. Client should call tick monotonically and use with 
 * a monotonic clock implementation.
 */
class Monotonic extends EventEmitter implements Window
{
  private $clock, $aggregator;
  
  public function __construct(Aggregator $aggregator, Clock $clock) {
    $this->clock = $clock;
    $this->aggregator = new \React\EEP\Util\Temporal( $aggregator, 
                                                      $this->clock->init());
    $this->aggregator->init();
  }
  
  public function enqueue($event) {
    $this->aggregator->accumulate(new Temporal($event, $this->clock->inc()));
  }
  
  public function tick() {
    // If time hasn't passed, we're done
    if (!$this->clock->tick()) {
      return;
    } 

    // Otherwise, emit if an interval worth of time has passed
    if ($this->clock->tock($this->aggregator->at())) {
      $this->emit('emit', array($this->aggregator->emit()->value));
      // this updates the at value of the aggregator for the next window
      $this->aggregator->update($this->clock->at());
      // 'close' current time window and 'open' a fresh one
      $this->aggregator->init(); 
    }
  }
}