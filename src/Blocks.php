<?php

namespace Giantpeach\Schnapps\Blocks;

use Giantpeach\Schnapps\Blocks\Cli\Cli;

abstract class Blocks
{
  public function __construct()
  {
    if (!class_exists("ACF")) {
      add_action("admin_notices", function () {
        echo '<div class="notice notice-error"><p>Advanced Custom Fields is required for Schnapps.</p></div>';
      });
      error_log("Warning: Advanced Custom Fields is required for Schnapps.");
    }

    new Cli();

    $this->registerTraits();
  }

  /**
   * registerTraits
   * @deprecated version 2.0.0
   *
   * @return void
   */
  protected function registerTraits()
  {
    trigger_error(
      "registerTraits is deprecated and will be removed in a future version.",
      E_USER_DEPRECATED,
    );

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
        if (
          in_array(
            "Giantpeach\Schnapps\Blocks\Traits\Prose",
            class_uses($block),
          )
        ) {
          // if the block uses the Prose trait, we'll need to add the fields
          $ref = new \ReflectionClass($block);
          $props = $ref->getProperty("blockName");
          $b = $props->getValue($ref);
          $proseBlocks[] = $b;
        }

        if (
          in_array(
            "Giantpeach\Schnapps\Blocks\Traits\Spacing",
            class_uses($block),
          )
        ) {
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
   * @deprecated version 2.0.0
   *
   * @param array $blocks The array of blocks to register.
   * @return void
   */
  protected function registerProseBlocks(array $blocks): void
  {
    trigger_error(
      "registerProseBlocks is deprecated and will be removed in a future version.",
      E_USER_DEPRECATED,
    );
    // Check if the blocks array is empty
    if (empty($blocks)) {
      return;
    }

    $locations = [];

    // Loop through each block and add it to the locations array
    foreach ($blocks as $block) {
      $locations[] = [
        [
          "param" => "block",
          "operator" => "==",
          "value" => $block,
        ],
      ];
    }

    // Add action to include fields
    add_action("acf/include_fields", function () {
      if (!function_exists("acf_add_local_field_group")) {
        return;
      }

      acf_add_local_field_group([
        "key" => "group_649c28616ae7b",
        "title" => "Typography Options",
        "fields" => [
          [
            "key" => "field_649c292def2b1",
            "label" => "Typography Options",
            "name" => "",
            "aria-label" => "",
            "type" => "accordion",
            "instructions" => "",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "open" => 0,
            "multi_expand" => 1,
            "endpoint" => 0,
          ],
          [
            "key" => "field_649c28613a461",
            "label" => "Colour",
            "name" => "colour",
            "aria-label" => "",
            "type" => "select",
            "instructions" => "",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "choices" => [
              "inherit" => "Inherit",
              "default" => "Default",
              "invert" => "Inverted",
            ],
            "default_value" => "inherit",
            "return_format" => "value",
            "multiple" => 0,
            "allow_null" => 0,
            "ui" => 0,
            "ajax" => 0,
            "placeholder" => "",
          ],
          [
            "key" => "field_649d4b9e6631d",
            "label" => "Size",
            "name" => "size",
            "aria-label" => "",
            "type" => "select",
            "instructions" => "",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "choices" => [
              "sm" => "Small",
              "base" => "Default",
              "lg" => "Large",
              "xl" => "Extra Large",
              "3xl" => 'the way we think of God\'s as big',
            ],
            "default_value" => "base",
            "return_format" => "value",
            "multiple" => 0,
            "allow_null" => 0,
            "ui" => 0,
            "ajax" => 0,
            "placeholder" => "",
          ],
          [
            "key" => "field_65c3a1ba56a87",
            "label" => "",
            "name" => "",
            "aria-label" => "",
            "type" => "accordion",
            "instructions" => "",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "open" => 0,
            "multi_expand" => 0,
            "endpoint" => 1,
          ],
        ],
        "location" => [
          [
            [
              "param" => "block",
              "operator" => "==",
              "value" => "giantpeach/slide",
            ],
          ],
          [
            [
              "param" => "block",
              "operator" => "==",
              "value" => "giantpeach/column",
            ],
          ],
          [
            [
              "param" => "block",
              "operator" => "==",
              "value" => "giantpeach/card",
            ],
          ],
          [
            [
              "param" => "block",
              "operator" => "==",
              "value" => "all",
            ],
          ],
        ],
        "menu_order" => 0,
        "position" => "normal",
        "style" => "default",
        "label_placement" => "top",
        "instruction_placement" => "label",
        "hide_on_screen" => "",
        "active" => false,
        "description" => "",
        "show_in_rest" => 0,
      ]);
    });
  }

  /**
   * Registers spacing blocks.
   * @deprecated version 2.0.0
   *
   * @param array $blocks The array of blocks to register.
   * @return void
   */
  protected function registerSpacingBlocks(array $blocks): void
  {
    trigger_error(
      "registerSpacingBlocks is deprecated and will be removed in a future version.",
      E_USER_DEPRECATED,
    );
    // Check if the blocks array is empty
    if (empty($blocks)) {
      return;
    }

    $locations = [];

    // Create location rules for each block
    foreach ($blocks as $block) {
      $locations[] = [
        [
          "param" => "block",
          "operator" => "==",
          "value" => $block,
        ],
      ];
    }

    // Add action to include fields
    add_action("acf/include_fields", function () use ($locations) {
      // Check if the acf_add_local_field_group function exists
      if (!function_exists("acf_add_local_field_group")) {
        return;
      }

      // Add local field group for block options
      acf_add_local_field_group([
        "key" => "block_options_group",
        "title" => "Block Options",
        "fields" => [
          // Add message field for block options
          [
            "key" => "block_options",
            "label" => "Block Options",
            "name" => "",
            "aria-label" => "",
            "type" => "message",
            "instructions" => "",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "message" => "",
            "new_lines" => "wpautop",
            "esc_html" => 0,
          ],
          // Add select field for block spacing
          [
            "key" => "block_spacing",
            "label" => "Block Spacing",
            "name" => "block_spacing",
            "aria-label" => "",
            "type" => "select",
            "instructions" => "Adjust the spacing for the block",
            "required" => 0,
            "conditional_logic" => 0,
            "wrapper" => [
              "width" => "",
              "class" => "",
              "id" => "",
            ],
            "choices" => [
              "default" => "Default",
              "short" => "Short",
              "tall" => "Tall",
              "none" => "None",
            ],
            "default_value" => "default",
            "return_format" => "value",
            "multiple" => 0,
            "allow_null" => 0,
            "ui" => 0,
            "ajax" => 0,
            "placeholder" => "",
          ],
        ],
        "location" => $locations,
        "menu_order" => 0,
        "position" => "normal",
        "style" => "default",
        "label_placement" => "top",
        "instruction_placement" => "label",
        "hide_on_screen" => "",
        "active" => true,
        "description" => "",
        "show_in_rest" => 0,
        "modified" => 1694005678,
      ]);
    });
  }

  /**
   * Generic block renderer
   * 
   * @since 2.0.0
   *
   * @return void
   */
  public static function renderBlock(
    $block,
    $content,
    $is_preview,
    $post_id,
    $wp_block,
    $context,
  ) {
    $path = $block["path"];

    //get the block name from the last part of path
    $blockName = explode("/", $path);
    $blockName = end($blockName);

    //get the block class
    $blockClass =
      "Giantpeach\\Schnapps\\Theme\\Blocks\\" . $blockName . "\\" . $blockName;

    //check if the class exists
    if (class_exists($blockClass)) {
      //create a new instance of the block
      $blockInstance = new $blockClass($block);
    }
  }
}
