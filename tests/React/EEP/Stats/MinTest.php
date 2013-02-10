<?php

namespace React\EEP\Stats;

class MinTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function minShouldBeTheMin() {
      $min = new Min();
      $min->init();
      $min->accumulate(1);
      $min->accumulate(10);
      $min->accumulate(100);
      $this->assertEquals(1, $min->emit());
      $min->accumulate(-10);
      $this->assertEquals(-10, $min->emit());
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
      $min = new Min();
      $min->init();
      $min->accumulate(1);
      $min->accumulate(5);
      $min->compensate(1);
      $this->assertEquals(5, $min->emit());
    }
}
