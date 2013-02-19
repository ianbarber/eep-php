<?php
require __DIR__.'/../vendor/autoload.php';

echo "Simple low rate detector\n";

$count_fn = new React\EEP\Stats\Count;
$win = new React\EEP\Window\Periodic($count_fn, 1000 * 5);

$win->on('emit', function($count) {
  if($count < 50) {
    echo "Alert - Low Rate! - $count\n";
  } else {
    echo "$count :)\n";
  }
});

while(true) {
  $win->enqueue(array(300, 4, 5, 10));
  $win->tick();
  usleep(100000 + rand(-20000, 20000));
}
