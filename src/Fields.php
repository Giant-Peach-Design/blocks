<?php

namespace Giantpeach\Schnapps\Blocks;

class Fields {
  public static function load(string $dir) {
    $blockName = static::getBlockNameFromDir($dir);

    // Try loading from PHP file first
    if (file_exists($dir . '/fields.php')) {
      $fields = require $dir . '/fields.php';

      if (is_array($fields)) {
        if (!function_exists('acf_add_local_field_group')) {
          return;
        }

        // Set block location if not already set
        $fields = static::setBlockLocation($fields, $blockName);

        acf_add_local_field_group($fields);
        return;
      }
    }

    // Try loading from JSON file if PHP file doesn't exist
    if (file_exists($dir . '/fields.json')) {
      $jsonContent = file_get_contents($dir . '/fields.json');
      $fields = json_decode($jsonContent, true);

      if (is_array($fields) && json_last_error() === JSON_ERROR_NONE) {
        if (!function_exists('acf_add_local_field_group')) {
          return;
        }

        // Generate keys for the field group and all fields if they don't exist
        $fields = static::generateKeys($fields, $blockName);

        // Set block location if not already set
        $fields = static::setBlockLocation($fields, $blockName);

        acf_add_local_field_group($fields);
      }
    }
  }

  /**
   * Generate ACF keys for field groups and fields that don't have them
   */
  private static function generateKeys(array $fields, string $blockName): array {
    // Extract just the block name part (e.g., "scrollvideo" from "giantpeach/scrollvideo")
    $blockShortName = str_replace('giantpeach/', '', $blockName);
    
    // Generate key for the field group if it doesn't exist
    if (!isset($fields['key'])) {
      $fields['key'] = 'group_' . $blockShortName;
    }

    // Generate keys for fields
    if (isset($fields['fields']) && is_array($fields['fields'])) {
      $fields['fields'] = static::generateFieldKeys($fields['fields'], $blockShortName);
    }

    return $fields;
  }

  /**
   * Recursively generate keys for fields and sub-fields
   */
  private static function generateFieldKeys(array $fields, string $blockName, string $prefix = ''): array {
    foreach ($fields as &$field) {
      if (!isset($field['key'])) {
        $fieldName = $field['name'] ?? 'unnamed';
        $keyName = $prefix ? $prefix . '_' . $fieldName : $blockName . '_' . $fieldName;
        $field['key'] = 'field_' . $keyName;
      }

      // Handle repeater/flexible content sub-fields
      if (isset($field['sub_fields']) && is_array($field['sub_fields'])) {
        $fieldName = $field['name'] ?? 'unnamed';
        $subPrefix = $prefix ? $prefix . '_' . $fieldName : $blockName . '_' . $fieldName;
        $field['sub_fields'] = static::generateFieldKeys($field['sub_fields'], $blockName, $subPrefix);
      }

      // Handle flexible content layouts
      if (isset($field['layouts']) && is_array($field['layouts'])) {
        $fieldName = $field['name'] ?? 'unnamed';
        foreach ($field['layouts'] as &$layout) {
          if (!isset($layout['key'])) {
            $layoutName = $layout['name'] ?? 'unnamed';
            $keyName = $prefix ? $prefix . '_' . $fieldName . '_' . $layoutName : $blockName . '_' . $fieldName . '_' . $layoutName;
            $layout['key'] = 'layout_' . $keyName;
          }
          if (isset($layout['sub_fields']) && is_array($layout['sub_fields'])) {
            $layoutName = $layout['name'] ?? 'unnamed';
            $subPrefix = $prefix ? $prefix . '_' . $fieldName . '_' . $layoutName : $blockName . '_' . $fieldName . '_' . $layoutName;
            $layout['sub_fields'] = static::generateFieldKeys($layout['sub_fields'], $blockName, $subPrefix);
          }
        }
      }

      // Handle group sub-fields
      if ($field['type'] === 'group' && isset($field['sub_fields']) && is_array($field['sub_fields'])) {
        $fieldName = $field['name'] ?? 'unnamed';
        $subPrefix = $prefix ? $prefix . '_' . $fieldName : $blockName . '_' . $fieldName;
        $field['sub_fields'] = static::generateFieldKeys($field['sub_fields'], $blockName, $subPrefix);
      }
    }

    return $fields;
  }

  /**
   * Get block name from directory path
   */
  private static function getBlockNameFromDir(string $dir): string {
    // Extract block name from directory path
    // e.g. /path/to/blocks/Hero -> Hero
    $blockName = basename($dir);
    
    // Convert PascalCase to kebab-case for ACF block names
    // e.g. TextImage -> text-image
    $blockName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $blockName));
    
    return 'giantpeach/' . $blockName;
  }

  /**
   * Set the block location for the field group
   */
  private static function setBlockLocation(array $fields, string $blockName): array {
    // Only set location if it doesn't already exist
    if (!isset($fields['location']) || empty($fields['location'])) {
      $fields['location'] = [
        [
          [
            'param' => 'block',
            'operator' => '==',
            'value' => $blockName
          ]
        ]
      ];
    }

    return $fields;
  }
}