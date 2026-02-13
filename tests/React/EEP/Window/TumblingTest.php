<?php

namespace React\EEP\Window;

use React\EEP\Clock\Counting;
use React\EEP\TestFn;

class TumblingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test  
     */
    public function windowShouldTumble() {
      $test_data = array(0, 1, 2, 4, 5, 6);
      $size = 3;
      $fn = new TestFn($this);
      $win = new Tumbling($fn, $size);
      $win->on("emit", function($data) use($size) {
        $this->assertEquals(count($data), $size, "Wrong count on emit");
      });
      $win->enqueue($test_data[0]);
      $win->enqueue($test_data[1]);
      $win->enqueue($test_data[2]);
      $win->enqueue($test_data[3]);
      $win->enqueue($test_data[4]);
      $win->enqueue($test_data[5]);
      $this->assertEquals(2, $fn->hasEmitted(), "Failed to tumble");
    }
}
