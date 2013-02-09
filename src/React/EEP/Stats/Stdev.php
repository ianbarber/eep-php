<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get the standard deviation of all the things
 */
class Stdev extends Variance implements Aggregator 
{
  public function emit() {
    return sqrt(parent::emit());
  }
}