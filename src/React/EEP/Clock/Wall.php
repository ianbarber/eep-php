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
    return $this->at;
  }
  
  public function inc() {
    $this->at = intval(microtime(true) * 1000);
  }
  
  public function tick() {
    if($this->mark === null) {
      $this->mark = $this->at + $this->interval;
    }
    return (($this->at - $this->mark) >= $this->interval);
  }
  
  public function tock($elapsed) {
    $delta = $this->at - $elapsed;
    if($delta >= $this->interval) {
      $this->mark += $this->interval;
      return true;
    }
    return false;
  }
}