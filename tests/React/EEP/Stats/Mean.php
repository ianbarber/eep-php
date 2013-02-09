<?php

namespace React\EEP\Stats;

class MeanTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function averageOfTwoNumbersShouldBeHalf()
    {
      $mean = new Mean();
      $mean->init();
      $mean->accumulate(4);
      $mean->accumulate(8);
      $this->assertEquals(6, $mean->emit());
    }
    
    public function compensateRemovesNumber()
    {
      $mean = new Mean();
      $mean->init();
      $mean->accumulate(4);
      $mean->accumulate(8);
      $mean->compensate(4);
      $this->assertEquals(8, $mean->emit());
    }
}
