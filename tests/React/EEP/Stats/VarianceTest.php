<?php

namespace React\EEP\Stats;

class VarianceTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function calculateVarianceAccurately() {
      $vars = new Variance();
      $vars->init();
      $vars->accumulate(1);
      $vars->accumulate(1);
      $this->assertEqualsWithDelta(0, $vars->emit(), 0.01);
      $vars->init();
      $vars->accumulate(5);
      $vars->accumulate(7);
      $vars->accumulate(9);
      $this->assertEqualsWithDelta(4, $vars->emit(), 0.01);
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $vars = new Variance();
      $vars->init();
      $vars->accumulate(5);
      $vars->accumulate(7);
      $vars->accumulate(9);
      $vars->compensate(5);
      $this->assertEqualsWithDelta(2, $vars->emit(), 0.01);
    }
}
