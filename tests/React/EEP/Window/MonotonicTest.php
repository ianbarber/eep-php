<?php

namespace React\EEP\Window;

use React\EEP\Clock\Counting;
use React\EEP\TestFn;

class MonotonicTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function triggerOnlyOnTick() {
      $test_data = array(0, 1, 2);
      $clock = new Counting();
      $clock->init();
      $fn = new TestFn($this);
      $win = new Monotonic($fn, $clock);
      $win->on("emit", function($data) use(&$test_data) {
        $this->assertEquals(count($data), count($test_data), 
            "Wrong count on emit");
        $this->assertEquals($data[0], $test_data[0], "Wrong data");
      });
      $win->enqueue($test_data[0]);
      $win->enqueue($test_data[1]);
      $win->enqueue($test_data[2]);
      $win->tick();
    }
}
