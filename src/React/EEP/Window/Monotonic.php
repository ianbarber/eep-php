<?php

namespace React\EEP\Window;

use React\EEP\Aggregator;
use React\EEP\Clock;
use React\EEP\Window;
use React\EEP\Temporal;
use Evenement\EventEmitter;

/**
 * A monotonic window. Client should call tick monotonically and use with 
 * a monotonic clock implementation.
 */
class Monotonic extends EventEmitter implements Window
{
  private $clock, $aggregator, $index;
  
  public function __construct(Aggregator $aggregator, Clock $clock) {
    $this->clock = $clock;
    $this->clock->init();
    $this->aggregator = new \React\EEP\Util\Temporal( $aggregator, 
                                                      $clock->init());
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