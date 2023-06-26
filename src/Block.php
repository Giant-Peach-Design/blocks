<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
use Giantpeach\Schnapps\Twiglet\Twiglet;

class Block implements BlockInterface
{
  static $blockName = 'giantpeach/block';
  protected $isAdmin = false;

  public function __construct()
  {
    if (is_admin()) {
      $this->isAdmin = true;
    }
  }

  public function render(): void
  {
    if (file_exists(self::getDir() . '/view.twig')) {
      Twiglet::getInstance()->display('/src/Blocks/' . self::getBlockNameFromDir() . '/view.twig', get_object_vars($this));
    } else {
      include self::getDir() . '/template.php';
    }
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

  private static function getBlockNameFromDir(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    return $reflector->getShortName();
  }

  private static function getDir(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    return dirname($reflector->getFileName());
  }
}
