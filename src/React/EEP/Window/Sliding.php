<?php

namespace React\EEP\Window;

use React\EEP\Aggregator;
use React\EEP\Window;
use Evenement\EventEmitter;

class Sliding extends EventEmitter implements Window
{
  private $size, $aggregator, $index, $mark;
  
  public function __construct(Aggregator $aggregator, $size) {
    $this->size = $size;
    $this->aggregator = $aggregator;
    $this->aggregator->init();
    $this->index = 0;
    $this->mark = $size - 1;
    $this->buffer = new \SPLFixedArray($size);
  }
  
  public function enqueue($event) {
    $this->aggregator->accumulate($event);
    if($this->index >= $this->mark) {
      $po = ($this->index + 1) % $this->size;
      $this->emit('emit', array($this->aggregator->emit()));
      $this->aggregator->compensate($this->buffer[$po]);
    }
    $this->buffer[$this->index % $this->size] = $event;
    $this->index++;
  }
}