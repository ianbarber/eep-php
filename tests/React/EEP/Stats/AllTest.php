<?php

namespace React\EEP\Stats;

class AllTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function allShouldBeTheMin() {
      $all = new All();
      $all->init();
      $all->accumulate(1);
      $all->accumulate(10);
      $all->accumulate(100);
      $this->assertEquals(100, $all->emit()['max']);
    }
    
    /** @test */
    public function compensateRemovesNumber() {
      $all = new All();
      $all->init();
      $all->accumulate(1);
      $all->accumulate(1);
      $all->accumulate(1);
      $all->accumulate(5);
      $all->compensate(5);
      $this->assertEquals(0, $all->emit()['vars']);
    }
}
