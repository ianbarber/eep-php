<?php

namespace React\EEP\Util;

use React\EEP\Aggregator;

/**
 * Do nothing with all the things.
 * 
 * Useful when you simply want to exploit window 'emit' events
 * to perform some action
 */
class Noop implements Aggregator 
{
  public function init() { }
  
  public function accumulate($_) { }
  
  public function compensate($_) { }
  
  public function emit() { }
}