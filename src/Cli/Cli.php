<?php

namespace Giantpeach\Schnapps\Blocks\Cli;

class Cli
{

  public function __construct()
  {
    add_action('cli_init', [$this, 'registerCommands']);
  }

  public function registerCommands()
  {
    \WP_CLI::add_command('hello', [$this, 'hello']);
    \WP_CLI::add_command('create-block', [$this, 'createBlock']);
  }

  public function createBlock($args, $assocArgs)
  {
    if (count($args) < 1) {
      \WP_CLI::error('Please provide a block name');
    }

    \WP_CLI::success('Creating block ' . $args[0]);

    $blockName = $args[0];
    $blockName = str_replace(' ', '-', $blockName);
    $blockName = strtolower($blockName);
    $blockName = 'giantpeach/' . $blockName;
    $className = ucfirst($args[0]);
    $variableName = "$" . lcfirst($args[0]);

    $blockPath = get_template_directory() . '/src/Blocks/' . $args[0];
    $blockTemplatePath = $blockPath . '/view.twig';
    $blockJsonPath = $blockPath . '/block.json';
    $blockClassPath = $blockPath . '/' . $args[0] . '.php';

    $renderCallback = sprintf("\\\Giantpeach\\\Schnapps\\\Theme\\\Blocks\\\%s\\\%s::display", $className, $className);

    $displayFunc = sprintf(
      "public static function display(): void {
        %s = new %s();
        %s->render();
      }",
      $variableName,
      $className,
      $variableName
    );

    if (!file_exists($blockPath)) {
      mkdir($blockPath);
    }

    if (!file_exists($blockTemplatePath)) {
      $template = <<<EOT
        <section class="{{ classes.block.name }} {{ classes.block.spacing }}">
          <div class="container ">
            <InnerBlocks className="prose {{ classes.prose.color }} max-w-none flex flex-wrap justify-center -mx-4" allowedBlocks="{{ allowedBlocks | wp_json_encode }}" />
          </div>
        </section>
      EOT;

      file_put_contents($blockTemplatePath, $template);

      \WP_CLI::success('Template created');
    } else {
      \WP_CLI::error('Template already exists');
    }

    if (!file_exists($blockJsonPath)) {
      $json = <<<EOT
      {
        "name": "$blockName",
        "title": "$className",
        "category": "giantpeach",
        "icon": "smiley",
        "keywords": [
          "giantpeach"
        ],
        "supports": {
          "jsx": true
        },
        "acf": {
          "mode": "preview",
          "renderCallback": "$renderCallback"
        }
      }
      EOT;

      file_put_contents($blockJsonPath, $json);

      \WP_CLI::success('Block JSON created');
    } else {
      \WP_CLI::error('Block JSON already exists');
    }

    if (!file_exists($blockClassPath)) {
      $class = <<<EOT
      <?php
      
      namespace Giantpeach\Schnapps\Theme\Blocks\\$className;
      
      use Giantpeach\Schnapps\Blocks\Interfaces\BlockInterface;
      use Giantpeach\Schnapps\Blocks\Block;

      class $className extends Block implements BlockInterface
      {
      
        public static string \$blockName = '$blockName';

        $displayFunc
      }
      EOT;

      file_put_contents($blockClassPath, $class);

      \WP_CLI::success('Block class created');
    } else {
      \WP_CLI::error('Block class already exists');
    }

    \WP_CLI::success('Block created');
    \WP_CLI::success('Don\'t forget to register the block in src/Schnapps.php');
  }

  public function hello()
  {
    \WP_CLI::line('Hello Worldz');
  }
}
