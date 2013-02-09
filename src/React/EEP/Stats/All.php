<?php

namespace React\EEP\Stats;

use React\EEP\Aggregator;

/**
 * Get all of the stats on all of the things
 */
class All implements Aggregator 
{
  private $delta, $n, $sum, $min, $max;
  private $mean, $m2;

  
  public function init() {
    $this->mean = $this->m2 = $this->delta = $this->n = $this->sum = 0;
    $this->min = $this->max = null;
  }
  
  public function accumulate($v) {
    if($v == null) {
      return;
    }
    
    $cur = $this->n;
    $this->sum += $v;
    
    if($this->min == null || $v < $this->min) {
      $this->min = $v;
    }
    
    if($this->max == null || $v > $this->max) {
      $this->max = $v;
    }
    
    $this->n++;
    
    $this->d = $v - $this->mean;
    $dn = $this->d / $this->n;
    $t1 = $this->d * $dn * $cur;
    $this->mean = $this->mean + $dn;
    $this->m2 = $this->m2 + $t1;
  }
  
  public function compensate($v) {
    if($v == null) {
      return;
    }
    
    $cur = $this->n;
    $this->sum -= $v;
    
    $this->n--;
    
    $this->d = $this->mean - $v;
    $dn = $this->d / $this->n;
    $t1 = $this->d * $dn * $cur;
    $this->mean = $dn;
    $this->m2 = $this->m2 - $t1;
  }
  
  public function emit() {
    $variance = $this->m2/($this->n-1);
    return array(
      "count" => $this->n,
      "sum" => $this->sum,
      "min" => $this->min,
      "max" => $this->max,
      "mean" => $this->mean,
      "vars" => $variance,
      "stdevs" =>  sqrt($variance)
    );
  }
}