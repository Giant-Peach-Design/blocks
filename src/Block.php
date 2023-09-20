<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
use Giantpeach\Schnapps\Twiglet\Twiglet;

class Block implements BlockInterface
{
  public static string $blockName = 'giantpeach/block';
  protected $isAdmin = false;

  /**
   * Block spacing classes, can be overwritten at the block level
   */
  public array $blockSpacing = [
    "default" => "py-8 md:py-16",
    "short" => "py-4 md:py-8",
    "tall" => "py-16 md:py-24",
    "none" => "py-0",
  ];

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
  }

  /**
   * Populate the classes array with classes that can be added to the block
   *
   * @return array
   */
  public function getClasses(): array
  {
    $classes = [];

    $classes['prose'] = [
      'color' => 'prose-' . get_field('colour') ?? 'inherit',
      'size' => 'prose-' . get_field('size') ?? 'base'
    ];

    $classes['block']['name'] = preg_replace('/[\W\s\/]+/', '-', self::getBlockName());
    $classes['block']['spacing'] = $this->blockSpacing[get_field('block_spacing') ?? 'default'];

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
    $reflector = new \ReflectionClass(get_called_class());
    $blockName = $reflector->getStaticPropertyValue('blockName');
    return $blockName;
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
