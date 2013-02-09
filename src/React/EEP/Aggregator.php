<?php

namespace React\EEP;

interface Aggregator  
{
  public function init();
  public function accumulate($event);
  public function compensate($event);
  public function emit();
  //public function make();
}