<?php 

namespace React\EEP\Clock;
use React\EEP\Clock;

class Counting implements Clock {
  private $at, $mark;
  
  public function __construct() {
    $this->mark = null;
  }
  
  public function at() {
    return $this->at;
  }
  
  public function init() {
    $this->at = $this->mark = 0;
    return $this->at;
  }
  
  public function inc() {
    $this->at++;
  }
  
  public function tick() {
    if($this->mark === null) {
      $this->mark = $this->at + 1;
    }
    return (($this->at - $this->mark) >= 1);
  }
  
  public function tock($elapsed) {
    $delta = $this->at - $elapsed;
    if($delta >= 1) {
      $this->mark += 1;
      return true;
    }
    return false;
  }
}