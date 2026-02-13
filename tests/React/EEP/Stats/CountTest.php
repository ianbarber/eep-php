<?php

namespace React\EEP\Stats;

class CountTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function countShouldCount() {
      $count = new Count();
      $count->init();
      $count->accumulate(1);
      $count->accumulate(1);
      $this->assertEquals(2, $count->emit());
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $count = new Count();
      $count->init();
      $count->accumulate(1);
      $count->accumulate(1);
      $count->compensate(1);
      $this->assertEquals(1, $count->emit());
    }
}
