<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Twiglet\Twiglet;

abstract class NewBlock {
  protected bool $isAdmin = false;
  public string $blockName = 'giantpeach/block';
  public string $id;

  public function __construct(...$args) {
    $this->id = uniqid();

    if (is_admin()) {
      $this->isAdmin = true;
    }

    $this->mount(...$args);
    $this->render();
  }

  public function mount(...$args): void {}

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
}