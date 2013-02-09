<?php

namespace React\EEP;

/**
 * Clocks tick and they tock, but not all clocks are equal
 */
interface Clock {
  public function init();
  
  /**
   * Returns the current time according to this type of clock
   */
  public function at();
  
  /**
   * Increments the current clocks time by a single moment
   */
  public function inc();

  /**
   * Stream operators call tick when they want to know if the clock has 
   * advanced since last checked
   */
  public function tick();
  
  /**
   * Stream operators call tock with a time x. if x happened now or 
   * before, returns true
   */
  public function tock($x);
  
}