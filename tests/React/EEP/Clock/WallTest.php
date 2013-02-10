<?php

namespace React\EEP\Clock;

class WallTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function clockShouldIncrement() {
      $clock = new Wall(1);
      $clock->init();
      $t = $clock->at();
      usleep(3000); // Sleep for a milli, a milli, a milli
      $clock->inc();
      $this->assertTrue($clock->tick(), "Clock has moved but did not tick");
      $this->assertGreaterThan($t, $clock->at());
    }
    
    /** @test */
    public function clockShouldTockEachTime() {
      $clock = new Wall(1);
      $clock->init();
      $to = intval(microtime(true) * 1000);
      $this->assertFalse($clock->tick(), "Tick without time moving");
      $this->assertFalse($clock->tock($to), "Tock without time moving");
      usleep(2000); 
      $clock->inc();
      $this->assertTrue($clock->tick(), "No tick, even though time inc'd");
      $to += 4;
      $this->assertFalse($clock->tock($to), "Tock without time moving");
      usleep(2000); 
      $clock->inc();
      $this->assertTrue($clock->tock($to), "No tock!");
    }
}
