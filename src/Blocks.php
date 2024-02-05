<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Cli\Cli;

class Blocks
{
  public function __construct()
  {
    new Cli();

    $this->registerTraits();
  }

  protected function registerTraits()
  {
    $reflection = new \ReflectionClass($this);

    if (!$reflection->hasProperty("blocks")) {
      return;
    }

    $props = $reflection->getProperty("blocks");

    if ($props->isInitialized($this)) {
      $blocks = $reflection->getProperty("blocks")->getValue($this);

      $proseBlocks = [];
      $spacingBlocks = [];

      foreach ($blocks as $block) {
        if (in_array('Giantpeach\Schnapps\Blocks\Traits\Prose', class_uses($block))) {
          // if the block uses the Prose trait, we'll need to add the fields
          $ref = new \ReflectionClass($block);
          $props = $ref->getProperty("blockName");
          $b = $props->getValue($ref);
          $proseBlocks[] = $b;
        }

        if (in_array('Giantpeach\Schnapps\Blocks\Traits\Spacing', class_uses($block))) {
          // if the block uses the Spacing trait, we'll need to add the fields
          $ref = new \ReflectionClass($block);
          $props = $ref->getProperty("blockName");
          $b = $props->getValue($ref);
          $spacingBlocks[] = $b;
        }
      }

      $this->registerProseBlocks($proseBlocks);
      $this->registerSpacingBlocks($spacingBlocks);
    }
  }


  /**
   * Registers Prose Blocks.
   *
   * @param array $blocks The array of blocks to register.
   * @return void
   */
  protected function registerProseBlocks(array $blocks): void
  {
    // Check if the blocks array is empty
    if (empty($blocks)) {
      return;
    }

    $locations = [];

    // Loop through each block and add it to the locations array
    foreach ($blocks as $block) {
      $locations[] = [
        [
          'param' => 'block',
          'operator' => '==',
          'value' => $block,
        ],
      ];
    }

    // Add action to include fields
    add_action('acf/include_fields', function () use ($locations) {
      // Check if the acf_add_local_field_group function exists
      if (!function_exists('acf_add_local_field_group')) {
        return;
      }

      // Add local field group for typography options
      acf_add_local_field_group(array(
        'key' => 'typography_options_group',
        'title' => 'Typography Options',
        'fields' => array(
          // Typography Options message field
          array(
            'key' => 'typography_options',
            'label' => 'Typography Options',
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => '',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
          ),
          // Typography Colour select field
          array(
            'key' => 'typography_colour',
            'label' => 'Colour',
            'name' => 'colour',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'inherit' => 'Inherit',
              'default' => 'Default',
              'invert' => 'Inverted',
            ),
            'default_value' => 'inherit',
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
          ),
          // Typography Size select field
          array(
            'key' => 'typography_size',
            'label' => 'Size',
            'name' => 'size',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'sm' => 'Small',
              'base' => 'Default',
              'lg' => 'Large',
              'xl' => 'Extra Large',
              '3xl' => 'the way we think of God\'s as big',
            ),
            'default_value' => 'base',
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
          ),
          // Message field for 3xl size
          array(
            'key' => 'field_64a5767937ba5',
            'label' => '',
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_649d4b9e6631d',
                  'operator' => '==',
                  'value' => '3xl',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => 'Dude, your references are out of control, everyone knows that.',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
          ),
        ),
        'location' => $locations,
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'modified' => 1695201294,
      ));
    });
  }

  /**
   * Registers spacing blocks.
   *
   * @param array $blocks The array of blocks to register.
   * @return void
   */
  protected function registerSpacingBlocks(array $blocks): void
  {
    // Check if the blocks array is empty
    if (empty($blocks)) {
      return;
    }

    $locations = [];

    // Create location rules for each block
    foreach ($blocks as $block) {
      $locations[] = [
        [
          'param' => 'block',
          'operator' => '==',
          'value' => $block,
        ],
      ];
    }

    // Add action to include fields
    add_action('acf/include_fields', function () use ($locations) {
      // Check if the acf_add_local_field_group function exists
      if (!function_exists('acf_add_local_field_group')) {
        return;
      }

      // Add local field group for block options
      acf_add_local_field_group(array(
        'key' => 'block_options_group',
        'title' => 'Block Options',
        'fields' => array(
          // Add message field for block options
          array(
            'key' => 'block_options',
            'label' => 'Block Options',
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => '',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
          ),
          // Add select field for block spacing
          array(
            'key' => 'block_spacing',
            'label' => 'Block Spacing',
            'name' => 'block_spacing',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => 'Adjust the spacing for the block',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'default' => 'Default',
              'short' => 'Short',
              'tall' => 'Tall',
              'none' => 'None',
            ),
            'default_value' => 'default',
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
          ),
        ),
        'location' => $locations,
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'modified' => 1694005678,
      ));
    });
  }
}
