<?php

namespace Giantpeach\Blocks;

use Giantpeach\Blocks\Cli\Cli;

class Blocks
{
  public function __construct()
  {
    new Cli();
  }
}

new Blocks();
