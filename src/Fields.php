<?php

namespace Giantpeach\Schnapps\Blocks;

class Fields {
  public static function load(string $dir) {

    if (file_exists($dir . '/fields.php')) {
      $fields = require $dir . '/fields.php';

      if (is_array($fields)) {
        if (!function_exists('acf_add_local_field_group')) {
          return;
        }

        acf_add_local_field_group($fields);
      }
    }
  }
}