<?php

namespace Giantpeach\Schnapps\Blocks\Traits;

use Giantpeach\Schnapps\Blocks\Classes;

/**
 * Trait Spacing
 * 
 * Using this trait will opt the block into receiving the spacing
 * classes and fields.
 * 
 * This might get removed in the future, as it's not really necessary
 * and was a feature that was added to the GP site for the designers
 * to go nuts with.
 */
trait Spacing
{
  public Classes $spacingClass;

  public array $blockSpacing = [
    "default" => "py-8 md:py-16",
    "short" => "py-4 md:py-8",
    "tall" => "py-16 md:py-24",
    "none" => "py-0",
  ];

  public function getSpacingClasses(): array
  {
    $classes = parent::getClasses();
    $this->spacingClass = new Classes();

    $spacing = get_field('block_spacing') ?? 'default';

    $this->spacingClass->add($this->blockSpacing[$spacing]);

    $rp = new \ReflectionProperty($this, 'wrapperClass');
    if ($rp->isInitialized($this)) {
      $this->wrapperClass->add($this->blockSpacing[$spacing]);
    }

    return $classes;
  }
}
