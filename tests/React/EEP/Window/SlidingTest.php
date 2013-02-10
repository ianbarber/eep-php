<?php

namespace React\EEP\Window;

use React\EEP\Clock\Counting;
use React\EEP\TestFn;

class SlidingTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function windowShouldSlide() {
      $test_data = array(0, 1, 2, 4, 5);
      $size = 3;
      $fn = new TestFn($this);
      $win = new Sliding($fn, $size);
      $win->on("emit", function($data) use(&$test_data, $size) {
        $this->assertEquals(count($data), $size, 
            "Wrong count on emit");
      });
      $win->enqueue($test_data[0]);
      $win->enqueue($test_data[1]);
      $win->enqueue($test_data[2]);
      $win->enqueue($test_data[3]);
      $win->enqueue($test_data[4]);
      $this->assertEquals(3, $fn->hasEmitted(), "Failed to slide");
    }
}
