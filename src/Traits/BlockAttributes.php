<?php

namespace Giantpeach\Schnapps\Blocks\Traits;

trait BlockAttributes {
  public array $blockAttributes = [];

  public function initBlockAttributes() {
    $this->blockAttributes = $this->getBlockAttributes();
    $this->wrapperClass->add($this->blockAttributes['class']);
  }

  private function getBlockAttributes(): array
  {
    $attrString = get_block_wrapper_attributes();
    $attrArray = current((array) new \SimpleXMLElement("<element " . $attrString . " />"));
    $attrArray['raw'] = $attrString;

    return $attrArray;
  }
}