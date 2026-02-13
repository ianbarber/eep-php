<?php

namespace React\EEP;

use React\EEP\Aggregator;

class TestFn implements Aggregator {
  private $test, $data, $has_emit;
  public function __construct(\PHPUnit\Framework\TestCase $test) {
    $this->test = $test;
    $this->data = null;
    $this->has_emit = 0;
  }
  public function init() {
    $this->data = array();
  }
  public function accumulate($v) {
    if($this->data === null) {
      $this->test->assertTrue(false, "Aggregator not initialised");
    }
    $this->data[] = $v;
  }
  public function compensate($v) {
    unset($this->data[array_search($v, $this->data)]);
  }
  public function emit() {
    $this->has_emit++;
    return $this->data;
  }
  public function hasEmitted() {
    return $this->has_emit;
  }
}