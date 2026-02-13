<?php

namespace React\EEP\Stats;

class StdevTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function calculateStdevAccurately() {
      $stdev = new Stdev();
      $stdev->init();
      $stdev->accumulate(1);
      $stdev->accumulate(1);
      $this->assertEqualsWithDelta(0, $stdev->emit(), 0.01);
      $stdev->init();
      $stdev->accumulate(5);
      $stdev->accumulate(7);
      $stdev->accumulate(9);
      $this->assertEqualsWithDelta(2, $stdev->emit(), 0.01);
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $stdev = new Stdev();
      $stdev->init();
      $stdev->accumulate(5);
      $stdev->accumulate(7);
      $stdev->accumulate(9);
      $stdev->compensate(5);
      $this->assertEqualsWithDelta(1.41421, $stdev->emit(), 0.01);
    }
}
