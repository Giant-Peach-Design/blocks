<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Twiglet\Twiglet;

abstract class Block {
  protected string $twigView = 'view.twig';
  protected string $phpView = 'template.php';

  protected bool $isAdmin = false;
  protected array $blockData = [];
  protected array $blockAttributes;

  public Classes $wrapperClass;
  public Style $style;

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
    $this->blockName = $this->blockData['name'];

    $this->style = new Style($this->id);

    $this->wrapperClass = new Classes();
    $this->wrapperClass->add('block-' . $this->id);
    $this->wrapperClass->add(preg_replace('/[\W\s\/]+/', '-', $this->blockName));

    $this->mount();
    $this->render();
  }

  public function mount(): void {}

  public function render(): string {
    $rp = new \ReflectionProperty($this, 'style');
    if ($rp->isInitialized($this)) {
      $this->style->render();
    }

    if (file_exists($this->getDir() . '/' . $this->twigView)) {
      echo $template = Twiglet::getInstance()->render('/src/Blocks/' . $this->getBlockNameFromDir() . '/' . $this->twigView, get_object_vars($this));
      return $template;
    } else {
      return include $this->getDir() . '/' . $this->phpView;
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
    
    // get directory of block
    $dir = dirname($reflector->getFileName());
    
    // load json file
    $json = file_get_contents($dir . '/block.json');
    $data = json_decode($json, true);

    return $data['name'];
  }
}