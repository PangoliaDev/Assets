# WordPress Assets Manager

Assets helper for WordPress development.

## Installation

Use composer to install the package.

````bash
composer require pangolia/assets
````

## Examples

````php
$assets_manager = new \Pangolia\Assets\AssetsManager([
  'version'     => '1.2.0',    
  'script_deps' => [],        
  'style_deps'  => [],      
  'in_footer'   => true,      
  'media'       => 'all',      
])

$assets_manager->collect( [
	[
		'handle' => 'my-script',
		'src'    => 'my_path/to/file.js',
		'deps'   => [ 'jquery' ]
	],
	[
		'handle' => 'my-stylesheet',
		'src'    => 'my_path/to/file.css',
	],
] )->enqueue();

$assets_manager->collect( [
	[
		'handle' => 'my-script',
		'src'    => 'my_path/to/file.js',
		'deps'   => [ 'jquery' ]
	],
	[
		'handle' => 'my-stylesheet',
		'src'    => 'my_path/to/file.css',
	],
] )->enqueue_on( is_single() || is_archive() );

$assets_manager->enqueue( [
    'handle' => 'my-script',
    'src'    => 'my_path/to/file.js',
    'deps'   => [ 'jquery' ]
] );

$assets_manager->collect( [
	[
		'handle' => 'my-script',
		'src'    => 'my_path/to/file.js',
		'deps'   => [ 'jquery' ]
	],
	[
		'handle' => 'my-stylesheet',
		'src'    => 'my_path/to/file.css',
	],
] )->register();

$assets_manager->collect( [
	[
		'handle' => 'my-script',
		'src'    => 'my_path/to/file.js',
		'deps'   => [ 'jquery' ]
	],
	[
		'handle' => 'my-stylesheet',
		'src'    => 'my_path/to/file.css',
	],
] )->register_on( fn() => my_inner_bool_func() );

$assets_manager->register( [
    'handle' => 'my-script',
    'src'    => 'my_path/to/file.js',
    'deps'   => [ 'jquery' ]
] );

$assets_manager->remove_scripts([ 'script-handle', 'style-handle' ]);
````