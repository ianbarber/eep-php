<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get the standard deviation of all the things
 */
class Variance implements Aggregator 
{
  private $m_n;
  private $new_m, $old_m;
  private $new_s, $old_s;
  
  public function init() {
    $this->m_n = 0;
  }
  
  public function accumulate($v) {
    $this->m_n += 1;
    
    if($this->m_n == 1) {
      $this->old_m = $this->new_m = $v;
      $this->old_s = 0;
    } else {
      $this->new_m = $this->old_m + ($v - $this->old_m)/$this->m_n;
      $this->new_s = $this->old_s + ($v - $this->old_m)*($v - $this->new_m);
      
      $this->old_m = $this->new_m;
      $this->old_s = $this->new_s;
    }
  }
  
  public function compensate($v) {
    $this->m_n -= 1;
    
    $this->new_m = $this->old_m + ($this->old_m - $v)/$this->m_n;
    $this->new_s = $this->old_s + ($this->old_m - $v)*($v - $this->new_m);
    
    $this->old_m = $this->new_m;
    $this->old_s = $this->new_s;
  }
  
  public function emit() {
    return ($this->m_n > 1) ? $this->new_s/($this->m_n - 1): 0;
  }
}