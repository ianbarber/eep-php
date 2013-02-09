<?php

namespace React\EEP\Window;

use React\EEP\Aggregator;
use React\EEP\Window;
use Evenement\EventEmitter;

class Tumbling extends EventEmitter implements Window
{
  private $size, $aggregator, $index;
  
  public function __construct(Aggregator $aggregator, $size) {
    $this->size = $size;
    $this->aggregator = $aggregator;
    $this->aggregator->init();
    $this->index = 0;
  }
  
  public function enqueue($event) {
    $this->aggregator->accumulate($event);
    $this->index += 1;
    if ($this->index == $this->size) {
      $this->emit('emit', array($this->aggregator->emit()));
      $this->index = 0;
    }
  }
}
