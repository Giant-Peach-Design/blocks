<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
use Giantpeach\Schnapps\Twiglet\Twiglet;

class Block implements BlockInterface
{
  public string $blockName = 'giantpeach/block';
  protected $isAdmin = false;

  /**
   * Array of classes that can be added to the block
   * @var array
   */
  public array $classes;

  public function __construct()
  {
    if (is_admin()) {
      $this->isAdmin = true;
    }

    $this->classes = $this->getClasses();
    $this->blockName = self::getBlockName();
  }

  /**
   * Populate the classes array with classes that can be added to the block
   *
   * @return array
   */
  public function getClasses(): array
  {
    $classes = [];

    if (get_field('colour')) {
      $classes['prose']['color'] = 'prose-' . get_field('colour');
    }

    if (get_field('block_spacing')) {
      $classes['block']['spacing'] = get_field('block_spacing');
    }

    return $classes;
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
    return 'giantpeach/block';
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
