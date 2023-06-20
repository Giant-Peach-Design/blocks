<?php

namespace Giantpeach\Blocks;

use Giantpeach\Blocks\Interfaces\BlockInterface;

class Block implements BlockInterface
{
  static $blockName = 'giantpeach/block';

  public function render(): void
  {
    include self::getDir() . '/template.php';
  }

  public static function getBlockName(): string
  {
    return self::$blockName;
  }

  public static function registerBlock(): void
  {
    register_block_type(self::getDir() . '/block.json');
  }

  public static function display(): void
  {
  }

  private static function getDir(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    return dirname($reflector->getFileName());
  }
}
