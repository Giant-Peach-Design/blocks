<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Cli\Cli;

class Blocks
{
  public function __construct()
  {
    new Cli();
  }
}

new Blocks();
