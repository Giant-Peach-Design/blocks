<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Twiglet\Twiglet;

abstract class Block {
  protected bool $isAdmin = false;
  protected array $blockData = [];
  protected array $blockAttributes;

  public string $blockName = 'giantpeach/block';
  public string $id;
  public array $allowedBlocks = [];
  public array $template = [];

  public function __construct(...$args) {
    $this->id = uniqid();

    if (is_admin()) {
      $this->isAdmin = true;
    }

    $this->blockAttributes = $this->getWpAttributes();
    $this->blockData = $args[0];
    $this->mount();
    $this->render();
  }

  public function mount(): void {}

  public function render(): string {
    if (file_exists($this->getDir() . '/view.twig')) {
      echo $template = Twiglet::getInstance()->render('/src/Blocks/' . $this->getBlockNameFromDir() . '/view.twig', get_object_vars($this));
      return $template;
    }

    return "";
  }

  private function getBlockNameFromDir(): string {
    $reflector = new \ReflectionClass(get_called_class());
    return $reflector->getShortName();
  }

  private function getDir(): string {
    $reflector = new \ReflectionClass(get_called_class());
    return dirname($reflector->getFileName());
  }

  /**
   * Retrieves the WordPress attributes for the block.
   *
   * @return array The WordPress attributes.
   */
  private function getWpAttributes(): array
  {
    $attrString = get_block_wrapper_attributes();
    $attrArray = current((array) new \SimpleXMLElement("<element " . $attrString . " />"));
    $attrArray['raw'] = $attrString;

    return $attrArray;
  }

  public static function registerFields(): void {
    $reflector = new \ReflectionClass(get_called_class());
    $dir = dirname($reflector->getFileName());
    Fields::load($dir);
  }

  public static function getBlockName(): string {
    $reflector = new \ReflectionClass(get_called_class());
    $blockName = $reflector->getProperty('blockName');
    return $blockName;
  }
}