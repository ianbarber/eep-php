<?php

namespace React\EEP\Window;

use React\EEP\Clock\Counting;
use React\EEP\TestFn;

class PeriodicTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function triggerOnlyOnTick() {
      $test_data = array(0, 1, 2, 4, 5, 6);
      $size = 3;
      $fn = new TestFn($this);
      $this->assertEquals(0, $fn->hasEmitted());
      $win = new Periodic($fn, 0);
      $win->on("emit", function($data) use($size) {
        $this->assertEquals(count($data), $size,  "Wrong count on emit");
      });
      $this->assertEquals(0, $fn->hasEmitted());
      
      // Time Window
      $win->enqueue($test_data[0]);
      $win->enqueue($test_data[1]);
      $win->enqueue($test_data[2]);
      $win->tick();
      
      // Time window 2. 
      $win->enqueue($test_data[3]);
      $win->enqueue($test_data[4]);
      $win->enqueue($test_data[5]);
      $win->tick();
      
      $this->assertEquals(2, $fn->hasEmitted(), "Failed to tick");
    }
    
    /** @test */
    public function timeKeepsOnSlipping() {
      $test_data = array(0, 1, 2, 4, 5, 6);
      $size = 3;
      $fn = new TestFn($this);
      $win = new Periodic($fn, 2); // 2 millisecond window
      $win->on("emit", function($data) use($size) {
        //$this->assertEquals(count($data), $size,  "Wrong count on emit");
      });
      
      // Time Window
      $win->enqueue($test_data[0]);
      $win->tick();
      $win->enqueue($test_data[1]);
      $win->tick();
      
      // Ensure the clock has advanced enough we'll be in a new period. 
      usleep(2100);
      $win->enqueue($test_data[2]);
      $win->tick(); // we should have a output now

      // Time window 2. We actually only update the clock in the enqueue.
      $win->enqueue($test_data[3]);
      $win->tick(); 
      $win->enqueue($test_data[4]);
      $win->tick();
      
      // Ensure the clock has advanced enough we'll be in a new period. 
      usleep(2100);
      
      $win->enqueue($test_data[5]);
      $win->tick();
      
      $this->assertEquals(2, $fn->hasEmitted(), "Failed to tick");
    }
}
