# giantpeach/blocks

![Schnapps](https://img.shields.io/badge/Peach-Schnapps-%23aea0fd?style=for-the-badge)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

## Description

This package aims to simplify block creation and usage in Wordpress. It is designed and included as part of the Peach Schnapps Framework.

It can be used independently, but is designed to work with the other packages in the framework.

Updates and changes to this package are dictated by the projects we work on and their needs, they may not necessarily be backwards compatible.

## Installation

`composer require giantpeach/blocks`

## Usage

The package consists of a couple of classes that can be extended to create custom blocks.

The `Block` class provides a number of methods to simplify block creation and usage and hides away the complexity (block registration, template loading etc) so your extended class can focus on the block's functionality.

The `Blocks` class handles the actual registration of all your blocks and the traits they use. Typically, you would extend this class in your theme and add your blocks to it.

### Quickstart

Once you've extended the `Blocks` class it registers a WP CLI command to create new blocks. You can use this to create a new block:

`wp giantpeach create-block MyBlock [--prose] [--spacing]`

This command will scaffold a new block within your theme, including the block controller class, the block json and the twig template.

Optionally you can specify whether the block should use the Prose or Spacing traits.

### Traits

The package includes a number of traits that can be used to add functionality to your blocks. These are:

- `Prose` - adds a `prose` and creates a few ACF fields for use with the Block to modify `prose` styles
- `Spacing` - adds `spacing` classes to the block wrapper and creates a few ACF fields for use with the Block to modify `spacing` styles. The `spacing` classes are based on the Tailwind CSS spacing classes, and can be overwritten on the `Block` by setting the `$blockSpacing` array.

### Additional Classes

The package also include a few additional classes that can be helpful to use with your blocks:

- `Classes` - a class to handle the creation of classes for use with your blocks. This class is used to create classes for the `prose` and `spacing` traits, but can also be used to create classes for any other purpose.
- `Style` - a class to handle inline styles for use with your blocks. Style tags will automatically be added to the head of the document.

## License

This package is open source and is licensed under the [MIT License](https://opensource.org/licenses/MIT).
