<?php
require __DIR__.'/../vendor/autoload.php';
use React\EEP\Aggregator;

$data = __DIR__ . '/data.txt';

//  Widefinder aggregate function
class WideFinderFunction implements Aggregator {
  private $re, $keys;
  
  public function __construct($regex) {
    $this->re = $regex;
  }
  
  public function init() {
    $this->keys = array();
  }
  
  public function accumulate($line) {
    preg_match($this->re, $line, $matches);
    if(count($matches) == 0) {
      return;
    }
    $key = $matches[1];
    if(!$this->keys[$key]) {
      $this->keys[$key] = 1;
    } else {
      $this->keys[$key]++;
    }
  }
  
  public function compensate($line) {
    preg_match($this->re, $line, $matches);
    if(count($matches) == 0) {
      return;
    }
    $key = $matches[1];
    $this->keys[$key]--;
  }
  
  public function emit() {
    return array($this->keys);
  }
}

$regex = "/GET \/ongoing\/When\/\d\d\dx\/(\d\d\d\d\/\d\d\/\d\d\/[^ .]+) /";
$fn = new WideFinderFunction($regex);
$clock = new React\EEP\Clock\Wall(0);
$win = new React\EEP\Window\Monotonic($fn, $clock);

// On emit, log top 10 hits to standard output
$win->on('emit', function($matches) {
  $all = array();
  foreach($matches as $url => $n) {
    $all[] = array("url" => $url, "n" => $n);
  }
  asort($all); 
  $i = 1;
  foreach(array_slice($all, 0, 10) as $match) {
    prinf("%d:\t%s with %d hits\n", $i++, $match['url'], $match["n"]);
  }
});

$data = file_get_contents("http://www.tbray.org/tmp/o10k.ap");
foreach($data as $line) {
  $win->enqueue($line);
}
$win->tick();
