<?php

namespace React\EEP\Clock;

class CountingTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function clockShouldIncrement() {
      $clock = new Counting();
      $clock->init();
      $t = $clock->at();
      $clock->inc();
      $this->assertTrue($clock->tick(), "Clock has moved but did not tick");
      $this->assertGreaterThan($t, $clock->at());
    }
    
    /** @test */
    public function clockShouldTockEachTime() {
      $clock = new Counting();
      $clock->init();
      $this->assertFalse($clock->tick(), "Tick without time moving");
      $this->assertFalse($clock->tock(1), "Tock without time moving");
      $clock->inc();
      $this->assertTrue($clock->tick(), "No tick, even though time inc'd");
      $this->assertFalse($clock->tock(2), "Tock without time moving");
      $clock->inc();
      $this->assertTrue($clock->tock(1), "No tock!");
    }
}
