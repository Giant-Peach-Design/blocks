<?php

namespace Giantpeach\Schnapps\Blocks\Compatability;

use Giantpeach\Schnapps\Blocks\Classes;
use Giantpeach\Schnapps\Blocks\Fields;
use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
use Giantpeach\Schnapps\Blocks\Style;
use Giantpeach\Schnapps\Twiglet\Twiglet;

/**
 * @deprecated version 2.0.0
 * 
 * The Block class is the base class for all blocks. It provides a number of
 * methods that can be used to render the block, register the block and get the
 * block name.
 * 
 * This class is deprecated but will remain in the codebase for the foreseeable
 * future. It is recommended that you use the new Block class instead.
 */
class Block implements BlockInterface
{
  /**
   * Determines whether block is being view in admin or front end.
   *
   * @var bool
   */
  protected $isAdmin = false;

  /**
   * Array of native Wordpress block HTML attributes. Can be added to the block
   * @var array
   */
  protected array $blockAttributes;

  /**
   * The name of the block.
   *
   * @var string
   */
  public static string $blockName = 'giantpeach/block';

  /**
   * Unique block ID
   *
   * @var string $id The ID of the block.
   */
  public string $id;

  /**
   * @var Style $style The style of the block.
   */
  public Style $style;

  /**
   * The wrapper class for the block.
   *
   * @var Classes
   */
  public Classes $wrapperClass;

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
   * 
   * @deprecated deprecated since v1.0.9. Use $wrapperClass, the Classes class 
   * or the various Traits (e.g. Prose) instead.
   * 
   * @var array
   */
  public array $classes;

  public function __construct()
  {
    $this->id = uniqid();
    $this->style = new Style($this->id);
    $this->wrapperClass = new Classes();

    if (is_admin()) {
      $this->isAdmin = true;
    }

    $this->classes = $this->getClasses();
    $this->blockAttributes = $this->getWpAttributes();

    if (in_array('Giantpeach\Schnapps\Blocks\Traits\Prose', class_uses($this))) {
      // if the block uses the Prose trait, call the getProseClasses method
      $this->getProseClasses();
    }

    if (in_array('Giantpeach\Schnapps\Blocks\Traits\Spacing', class_uses($this))) {
      // if the block uses the Spacing trait, call the getSpacingClasses method
      $this->getSpacingClasses();
    }
  }

  /**
   * Populate the classes array with classes that can be added to the block
   *
   * @return array
   */
  public function getClasses(): array
  {
    $classes = [];

    $classes['block']['name'] = preg_replace('/[\W\s\/]+/', '-', self::getBlockName());
    $classes['block']['spacing'] = $this->blockSpacing[get_field('block_spacing') ?? 'default'];

    $this->wrapperClass->add('block-' . $this->id);
    $this->wrapperClass->add(preg_replace('/[\W\s\/]+/', '-', self::getBlockName()));

    //if (get_field('block_spacing')) {
    //  $this->wrapperClass->add($this->blockSpacing[get_field('block_spacing') ?? 'default']);
    //}

    return $classes;
  }

  /**
   * Renders the block.
   *
   * @return string The rendered block.
   */
  public function render(): string
  {
    // Render the block styles, checks if the property has been initialised first
    $rp = new \ReflectionProperty($this, 'style');
    if ($rp->isInitialized($this)) {
      $this->style->render();
    }

    // Generate the wrapper classes string, checks if the property has been initialised first
    $rp = new \ReflectionProperty($this, 'wrapperClass');
    if ($rp->isInitialized($this)) {
      //$this->wrapperClass = $this->_wrapperClasses->raw();
    }

    if (file_exists(self::getDir() . '/view.twig')) {
      echo $template = Twiglet::getInstance()->render('/src/Blocks/' . self::getBlockNameFromDir() . '/view.twig', get_object_vars($this));
      return $template;
    } else {
      return include self::getDir() . '/template.php';
    }
  }

  /**
   * Returns the name of the block.
   *
   * @return string The name of the block.
   */
  public static function getBlockName(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    $blockName = $reflector->getStaticPropertyValue('blockName');
    return $blockName;
  }

  /**
   * Registers the block.
   *
   * @return void
   */
  public static function registerBlock(): void
  {
    register_block_type(self::getDir() . '/block.json');
  }

  public static function registerFields(): void {
    Fields::load(self::getDir());
  }

  public static function display(): void
  {
  }

  /**
   * Retrieves the WordPress attributes for the block.
   *
   * @return array The WordPress attributes.
   */
  private static function getWpAttributes(): array
  {
    $attrString = get_block_wrapper_attributes();
    $attrArray = current((array) new \SimpleXMLElement("<element " . $attrString . " />"));
    $attrArray['raw'] = $attrString;

    return $attrArray;
  }

  /**
   * Retrieves the block name from the directory.
   *
   * @return string The block name.
   */
  private static function getBlockNameFromDir(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    return $reflector->getShortName();
  }

  /**
   * Returns the directory path.
   *
   * @return string The directory path.
   */
  private static function getDir(): string
  {
    $reflector = new \ReflectionClass(get_called_class());
    return dirname($reflector->getFileName());
  }
}
