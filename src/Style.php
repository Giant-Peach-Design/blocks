<?php

namespace Giantpeach\Schnapps\Blocks;

/**
 * Class Style
 * 
 * Represents a style for a block.
 */
class Style
{
  protected string $id;
  protected array $blockStyles = [];

  /**
   * Constructs a new Style instance.
   *
   * @param string $id The ID of the block.
   */
  public function __construct(string $id)
  {
    $this->id = $id;

    $wrapperAttributes = $this->getWrapperStyleAttribute();

    if (!empty($wrapperAttributes)) {
      $this->set(".block-{$this->id}", $wrapperAttributes);
    }
  }

  /**
   * Retrieves the block styles.
   *
   * @return array The block styles.
   */
  public function get(): array
  {
    return $this->blockStyles;
  }

  /**
   * Sets a style value for a given key.
   *
   * @param string $key The key for the style value.
   * @param array $value The value to set for the style key.
   * @return array The updated block styles array.
   */
  public function set(string $key, array $value): array
  {
    if ($key === "{id}") {
      $key = ".block-{$this->id}";
    }

    foreach ($value as $k => $v) {
      if ($k === "{id}") {
        $value[".block-{$this->id}"] = $v;
      }
    }

    $this->blockStyles[$key] = $value;

    return $this->blockStyles;
  }

  /**
   * Sets the styles for the block.
   *
   * @param array $styles The styles to set.
   * @return array The updated block styles.
   */
  public function setStyles(array $styles): array
  {
    $this->blockStyles = $styles;

    return $this->blockStyles;
  }

  /**
   * Renders the styles by adding them to the head of the document.
   */
  public function render()
  {
    $this->addStylesToHead();
  }

  /**
   * Retrieves the wrapper style attribute as an array.
   *
   * @return array The wrapper style attribute as an array.
   */
  protected function getWrapperStyleAttribute(): array
  {
    $attributes = get_block_wrapper_attributes();
    $attrArray = current((array) new \SimpleXMLElement("<element " . $attributes . " />"));
    if (isset($attrArray['style'])) {
      return $this->cssLinesToArray($attrArray['style']);
    }

    return [];
  }

  /**
   * Converts a string of CSS lines into an array.
   *
   * @param string $lines The CSS lines to convert.
   * @return array The converted CSS lines as an array.
   */
  protected function cssLinesToArray(string $lines): array
  {
    $arr = explode(';', $lines);
    $cssArray = [];

    foreach ($arr as $line) {
      if (empty($line)) {
        continue;
      }

      $cssArray = array_merge($cssArray, $this->cssLineToArray($line));
    }

    return $cssArray;
  }

  /**
   * Converts a CSS line string into an associative array.
   *
   * @param string $string The CSS line string to convert.
   * @return array The associative array representation of the CSS line.
   */
  protected function cssLineToArray(string $string): array
  {
    $arr = explode(':', $string);

    // if $arr[1] contains a semicolon, remove it
    if (strpos($arr[1], ';') !== false) {
      $arr[1] = str_replace(';', '', $arr[1]);
    }

    return [$arr[0] => $arr[1]];
  }

  /**
   * Converts an array of CSS properties and values into a string representation.
   *
   * @param array $cssArray The array of CSS properties and values.
   * @return string The string representation of the CSS properties and values.
   */
  protected function cssArrayToString(array $cssArray): string
  {
    $cssString = '';

    foreach ($cssArray as $key => $value) {
      if (is_array($value)) {
        $cssString .= $key . '{' . $this->cssArrayToString($value) . '}';
      } else {
        $cssString .= $key . ':' . $value . ';';
      }
    }

    return $cssString;
  }

  /**
   * Adds the styles to the head section of the page.
   */
  protected function addStylesToHead(): void
  {
    $styles = $this->get();

    if (empty($styles)) {
      return;
    }

    if (is_admin()) {
      echo '<style>' . $this->cssArrayToString($styles) . '</style>';
    }

    $styleString = $this->cssArrayToString($styles);

    wp_register_style('block-' . $this->id, false);
    wp_enqueue_style('block-' . $this->id);
    wp_add_inline_style('block-' . $this->id, $styleString);
  }
}
