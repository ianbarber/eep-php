<?php

namespace React\EEP\Stats;

class MeanTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function averageOfTwoNumbersShouldBeHalf() {
      $mean = new Mean();
      $mean->init();
      $mean->accumulate(4);
      $mean->accumulate(8);
      $this->assertEquals(6, $mean->emit());
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $mean = new Mean();
      $mean->init();
      $mean->accumulate(4);
      $mean->accumulate(8);
      $mean->compensate(4);
      $this->assertEquals(8, $mean->emit());
    }
}
