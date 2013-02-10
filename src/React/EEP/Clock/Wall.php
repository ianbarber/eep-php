<?php 

namespace React\EEP\Clock;
use React\EEP\Clock;

/**
 * Wall clock. Watch out for NTP/PTP shenanigans
 */
class Wall implements Clock {
  private $at, $mark, $interval;
  
  public function __construct($interval) {
    $this->interval = $interval;
    $this->mark = null;
  }
  
  public function at() {
    return $this->at;
  }
  
  public function init() {
    $this->at = intval(microtime(true) * 1000);
    $this->mark = $this->at + $this->interval;
    return $this->at;
  }
  
  public function inc() {
    $this->at = intval(microtime(true) * 1000);
    return $this->at;
  }
  
  public function tick() {
    if($this->mark === null) {
      $this->mark = $this->at + $this->interval;
    }
    
    // The mark is our point in time to trip over. If 
    // its in the future ("at") being the current time
    // we return false. If it's in the past then at - mark
    // will be positive, and we can tick. The tock() below 
    // will handle an update. 
    return ($this->at - $this->mark) >= 0;
  }
  
  public function tock($time) {
    // If time is in the past, at (now) - time will yield a 
    // positive number. If a window has passed (>= interval)
    // then we update the mark and move on. 
    $delta = $this->at - $time;
    if($delta >= $this->interval) {
      // Added this check to ensure we don't race the clock forwards
      // with some reckless tocking.
      if($this->tick()) {
        $this->mark += $this->interval;
      }
      return true;
    }
    return false;
  }
}