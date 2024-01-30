<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
use Giantpeach\Schnapps\Twiglet\Twiglet;

class Block implements BlockInterface
{
  public static string $blockName = 'giantpeach/block';
  protected $isAdmin = false;
  protected Classes $_wrapperClasses;

  public string $id;
  public Style $style;
  public string $wrapperClass = '';

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

  /**
   * Array of native Wordpress block HTML attributes. Can be added to the block
   * @var array
   */
  protected array $blockAttributes;

  public function __construct()
  {
    $this->id = uniqid();
    $this->style = new Style($this->id);
    $this->_wrapperClasses = new Classes();

    if (is_admin()) {
      $this->isAdmin = true;
    }

    $this->classes = $this->getClasses();
    $this->blockAttributes = $this->getWpAttributes();
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


    if (get_field('colour')) {
      $this->_wrapperClasses->add('prose-' . get_field('colour'));
    }

    if (get_field('size')) {
      $this->_wrapperClasses->add('prose-' . get_field('size'));
    }

    if (get_field('block_spacing')) {
      $this->_wrapperClasses->add($this->blockSpacing[get_field('block_spacing') ?? 'default']);
    }

    return $classes;
  }

  public function render(): string
  {
    // Render the block styles, checks if the property has been initialised first
    $rp = new \ReflectionProperty($this, 'style');
    if ($rp->isInitialized($this)) {
      $this->style->render();
    }

    // Generate the wrapper classes string, checks if the property has been initialised first
    $rp = new \ReflectionProperty($this, '_wrapperClasses');
    if ($rp->isInitialized($this)) {
      $this->wrapperClass = $this->_wrapperClasses->raw();
    }

    if (file_exists(self::getDir() . '/view.twig')) {
      echo $template = Twiglet::getInstance()->render('/src/Blocks/' . self::getBlockNameFromDir() . '/view.twig', get_object_vars($this));
      return $template;
    } else {
      return include self::getDir() . '/template.php';
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

  private static function getWpAttributes(): array
  {
    $attrString = get_block_wrapper_attributes();
    $attrArray = current((array) new \SimpleXMLElement("<element " . $attrString . " />"));
    $attrArray['raw'] = $attrString;

    return $attrArray;
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
