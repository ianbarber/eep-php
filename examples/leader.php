<?php
require __DIR__.'/../vendor/autoload.php';
use React\EEP\Window;
use Evenement\EventEmitter;

// Setup a combined window type and aggregator as an example of 
// the type of thing we could do. It would be much better to 
// decompose this into its constituent elements.

// Note, this could likely be sped up with power of 2 window sizes
// and bit-shifts instead of modulos.
class LeaderFinder extends EventEmitter implements Window {
  private $size, $buffers, $index, $sub, $mark, $aggregates;
  public function __construct($size, $sub_window_size) {
    
    // We'll keep a master buffer of $size.
    $this->size = $size;
    $this->buffers = new SplFixedArray(2);
    $this->buffers[0] = new SplFixedArray($size);
    $this->buffers[1] = new SplFixedArray($size);
    $this->index = new SplFixedArray(2);
    $this->index[0] = $this->index[1] = 0;
    
    // This is used to divide the main window into the lagged offsets.
    $this->sub = $sub_window_size;
    
    // Don't emit until we have collected one master set.
    $this->mark = $size;
    
    // Maintain a set of aggregate functions.
    $num_splits = $this->size / $this->sub;
    $this->aggregates = new SplFixedArray(2);
    $this->aggregates[0] = new SplFixedArray($num_splits);
    $this->aggregates[1] = new SplFixedArray($num_splits);
    for($i = 0; $i < $num_splits; $i++) {
      $this->aggregates[0][$i] = new React\EEP\Stats\All();
      $this->aggregates[0][$i]->init();
      $this->aggregates[1][$i] = new React\EEP\Stats\All();
      $this->aggregates[1][$i]->init();
    }
  }
  
  public function enqueue($event) {
    // We have to be a bit clever here to get the right windows.
    foreach($this->aggregates[$event->stream] as $i => $fn) {
      $offset = $this->index[$event->stream] - ($i * $this->sub);
      
      // If we have enough events for the lagged stream.
      if($offset > 0) {
        $fn->accumulate($event->value);
      }
      
      // If we have a full window.
      if($this->index[$event->stream] > $this->mark) {
        $po = ($offset + 1) % $this->size;
        $fn->compensate($this->buffers[$event->stream][$po]);
      }
    }
    
    // If both streams are past the mark, we can correlate
    $other = $event->stream ^ 1;
    $max_cor = 0;
    $cor = 0;
    if($this->index[$event->stream] > $this->mark && 
      $this->index[$other] > $this->mark ) {
        // Cross compare windows
        foreach($this->aggregates[0] as $i => $fn) {
          foreach($this->aggregates[1] as $j => $ofn) {
            $xo = ($this->index[0] - ((1+$i) * $this->sub)) % $this->size;
            $x_stats = $fn->emit();
            $yo = ($this->index[1] - ((1+$j) * $this->sub)) % $this->size;
            $y_stats = $ofn->emit();
            $c = $this->correlate($xo, $x_stats, $yo, $y_stats);
            if(abs($c) - abs($max_cor) > 0.01) {
              $max_cor = $c;
              $cor = $i - $j;
            }
           }
        }
    }
    
    // If 0, we have no leader
    if($cor != 0) {
      $this->emit('emit', array(array($cor, $max_cor)));
    }
    
    $buf_index = $this->index[$event->stream] % $this->size;
    $this->buffers[$event->stream][$buf_index] = $event->value;
    $this->index[$event->stream] += 1;
  }
  
  /**
   * Bog Standard Pearson correlation
   * correlation(x,y) = mean((x - mean(x)) * (y - mean(y))) / 
   *                     (stddev(x) * stddev(y))
   */
  private function correlate($xo, $x_stats, $yo, $y_stats) {
    $acc = 0;
    for($i = 0; $i < $this->sub; $i++) {
      $acc += ($this->buffers[0][($xo + $i) % $this->size] - $x_stats['mean']) *
        ($this->buffers[1][($yo + $i) % $this->size] - $y_stats['mean']);
    }
    $acc /= $this->sub;
    $stddevs = ($x_stats['stdevs'] * $y_stats['stdevs']);
    return ($stddevs == 0 ? 0 : $acc /$stddevs) ;
  }
}

// Lets calculate 4 subwindows.
$size = 12; $subwin = 3;
$win = new LeaderFinder($size, $subwin);
$win->on('emit', function($correlation) use ($subwin) {
  list($lead, $follow) = $correlation[0] > 0 ? array(0, 1) : array(1, 0);
  $diff = abs($correlation[0]) * $subwin;
  printf("Stream %d leads stream %d by %d ticks with a %.2f correlation\n", 
    $lead, $follow, $diff, $correlation[1]);
});

// Generate some correlated streams
$a = array(1, 1, 1, 3, 3, 3, 5, 5, 5, 7, 7, 7, 9, 9, 9, 11, 11, 11);
$b = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 3, 3, 3, 5, 5, 5, 7, 7, 7);

for($i = 0; $i < count($a); $i++) {
  $win->enqueue(new React\EEP\Event\Muxed($a[$i], 0));
  $win->enqueue(new React\EEP\Event\Muxed($b[$i], 1));
}