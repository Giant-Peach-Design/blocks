<?php

if (!function_exists('get_image_field') && function_exists('get_field')) {

  /**
   * Wrapper for ACF get_field function to get an image ID from field
   * regardless of the field return type.
   *
   * @param string $fieldName
   * @return int|null
   */
  function get_image_field(string $fieldName): ?int
  {
    $field = get_field($fieldName);

    if (is_array($field)) {
      return $field['ID'];
    }

    return $field;
  }

}