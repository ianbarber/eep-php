<?php

namespace React\EEP\Stats;

class MaxTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function maxShouldBeTheMin() {
      $max = new Max();
      $max->init();
      $max->accumulate(1);
      $max->accumulate(10);
      $max->accumulate(100);
      $this->assertEquals(100, $max->emit());
      $max->accumulate(-10);
      $max->accumulate(150);
      $this->assertEquals(150, $max->emit());
      $max->init();
      $max->accumulate(10);
      $this->assertEquals(10, $max->emit());
    }
    
    /** 
     * @test 
     *
     * So this test is skipped, because compensate doesn't work. Need to 
     * think up a way of doing this without effective keeping the whole
     * window in memory. 
     */
    public function compensateRemovesNumber() {
      $this->markTestIncomplete(); return;
      $max = new Max();
      $max->init();
      $max->accumulate(1);
      $max->accumulate(5);
      $max->compensate(5);
      $this->assertEquals(1, $max->emit());
    }
}
