<?php

namespace React\EEP\Stats;

class SumTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function sumUpSomeNumbers() {
      $sum = new Sum();
      $sum->init();
      $sum->accumulate(4);
      $sum->accumulate(8);
      $sum->accumulate(2);
      $this->assertEquals(14, $sum->emit());
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $sum = new Sum();
      $sum->init();
      $sum->accumulate(4);
      $sum->accumulate(8);
      $sum->compensate(4);
      $this->assertEquals(8, $sum->emit());
    }
}
