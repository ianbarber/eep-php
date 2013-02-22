<?php
require __DIR__.'/../vendor/autoload.php';

$mean_fn = new React\EEP\Stats\Mean;
$awin = new React\EEP\Window\Tumbling($mean_fn, 100);

$awin->on('emit', function($avg) {
  if($avg > 290) {
    printf("Average %dms - ALERT!\n", $avg);
  }
});

$start = microtime(true);
for($i = 0; $i < 50000; $i++) {
  $var = 275 + rand(-150, 150);
  $awin->enqueue($var);
}

echo "\n\n#########\n\n";

echo "Monitor combined variables\n";

$all_fn = new React\EEP\Stats\All;
$all_win = new React\EEP\Window\Tumbling($all_fn, 100);

$all_win->on('emit', function($vals) {
  if($vals['stdevs'] > 92 && $vals['mean'] > 280 && $vals['max'] > 400) {
    printf("Stddev %.2fms Average %dms Max %dms - ALERT!\n", 
      $vals['stdevs'], $vals['mean'], $vals['max']);
  }
});

$start = microtime(true);
for($i = 0; $i < 50000; $i++) {
  $var = 275 + rand(-150, 150);
  $all_win->enqueue($var);
}