<?php

namespace Giantpeach\Schnapps\Blocks\Traits;

use Giantpeach\Schnapps\Blocks\Classes;

/**
 * Trait Prose
 * 
 * Using this trait will opt the block in to receiving the Prose
 * classes and typography settings.
 * 
 * You will still need to output the classes in the block's template
 * e.g. with {{ proseClass }}
 */
trait Prose
{
  public Classes $proseClass;

  public function getProseClasses(): array
  {
    $classes = parent::getClasses();
    $this->proseClass = new Classes();

    $colour = get_field('colour') ?? 'inherit';
    $size = get_field('size') ?? 'base';

    $this->proseClass->add('prose-' . $colour);
    $this->proseClass->add('prose-' . $size);

    return $classes;
  }
}
