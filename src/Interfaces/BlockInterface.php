<?php

namespace Giantpeach\Schnapps\Blocks\Interfaces;

interface BlockInterface
{
  /**
   * Render the block
   *
   * @return void
   */
  public function render();

  /**
   * Register the block with WordPress
   *
   * @return void
   */
  public static function registerBlock();

  /**
   * Used as the ACF render callback
   *
   * @return void
   */
  public static function display();

  public static function getBlockName();
}
