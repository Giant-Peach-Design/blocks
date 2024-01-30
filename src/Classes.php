<?php

namespace Giantpeach\Schnapps\Blocks;

use Countable;

class Classes implements Countable
{
  protected array $classes = [];

  public function count(): int
  {
    return count($this->classes);
  }

  public function __construct(array|string $classes = [])
  {
    if (is_string($classes)) {
      $this->classes = explode(' ', $classes);
    } else {
      $this->classes = $classes;
    }
  }

  public function get(): array
  {
    return $this->classes;
  }

  public function add(string $value): array
  {
    $this->classes[] = $value;

    return $this->classes;
  }

  public function set(int $key, string $value): array
  {
    $this->classes[$key] = $value;

    return $this->classes;
  }

  public function remove(int $key): array
  {
    unset($this->classes[$key]);

    return $this->classes;
  }

  public function addClasses(array $classes): array
  {
    $this->classes = array_merge($this->classes, $classes);

    return $this->classes;
  }

  public function removeClasses(array $classes): array
  {
    $this->classes = array_diff($this->classes, $classes);

    return $this->classes;
  }

  public function raw(): string
  {
    return implode(' ', $this->classes);
  }
}
