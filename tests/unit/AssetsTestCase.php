<?php

namespace Pangolia\AssetsTests\Unit;

use Brain\Monkey;
use Pangolia\Assets\AssetsManager;
use PHPUnit\Framework\TestCase;

class AssetsTestCase extends TestCase {
	protected AssetsManager $assets_manager;
	protected array $global_script_deps;
	protected array $global_style_deps;
	protected string $assets_version;
	protected bool $in_footer;
	protected string $media;
	protected array $wp_assets;

	/**
	 * Setup which calls \WP_Mock setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->setUpGlobalSettings();
		$this->setUpAssetsManager();
		$this->setUpMockedWpAssets();
		Monkey\setUp();
		Monkey\Functions\when( '__' )->returnArg( 1 );
		Monkey\Functions\when( '_e' )->returnArg( 1 );
		Monkey\Functions\when( '_n' )->returnArg( 1 );
		Monkey\Functions\when( 'wp_enqueue_script' )->alias( function (
			$handle, $src = '', $deps = array(), $ver = false, $in_footer = false
		) {
			$this->wp_assets['enqueued']['scripts'][ $handle ] = [
				'handle'    => $handle,
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
			];
		} );
		Monkey\Functions\when( 'wp_register_script' )->alias( function (
			$handle, $src, $deps = array(), $ver = false, $in_footer = false
		) {
			$this->wp_assets['registered']['scripts'][ $handle ] = [
				'handle'    => $handle,
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
			];
		} );
		Monkey\Functions\when( 'wp_enqueue_style' )->alias( function (
			$handle, $src = '', $deps = array(), $ver = false, $media = 'all'
		) {
			$this->wp_assets['enqueued']['styles'][ $handle ] = [
				'handle' => $handle,
				'src'    => $src,
				'deps'   => $deps,
				'ver'    => $ver,
				'media'  => $media,
			];
		} );
		Monkey\Functions\when( 'wp_register_style' )->alias( function (
			$handle, $src, $deps = array(), $ver = false, $media = 'all'
		) {
			$this->wp_assets['registered']['styles'][ $handle ] = [
				'handle' => $handle,
				'src'    => $src,
				'deps'   => $deps,
				'ver'    => $ver,
				'media'  => $media,
			];
		} );
		Monkey\Functions\when( 'wp_deregister_style' )->alias( function ( $handle ) {
			unset( $this->wp_assets['registered']['styles'][ $handle ] );
		} );
		Monkey\Functions\when( 'wp_deregister_script' )->alias( function ( $handle ) {
			unset( $this->wp_assets['registered']['scripts'][ $handle ] );
		} );
		Monkey\Functions\when( 'wp_dequeue_style' )->alias( function ( $handle ) {
			unset( $this->wp_assets['enqueued']['styles'][ $handle ] );
		} );
		Monkey\Functions\when( 'wp_dequeue_script' )->alias( function ( $handle ) {
			unset( $this->wp_assets['enqueued']['scripts'][ $handle ] );
		} );
	}

	public function setUpGlobalSettings() {
		$this->global_script_deps = [ 'my-script-dep' ];
		$this->global_style_deps = [ 'my-style-dep' ];
		$this->assets_version = '1.2.0';
		$this->in_footer = true;
		$this->media = 'all';
	}

	public function setUpAssetsManager() {
		$this->assets_manager = new AssetsManager( [
			'version'     => $this->assets_version,
			'script_deps' => $this->global_script_deps,
			'style_deps'  => $this->global_style_deps,
			'in_footer'   => $this->in_footer,
			'media'       => $this->media,
		] );
	}

	public function setUpMockedWpAssets() {
		$this->wp_assets = [
			'enqueued'   => [
				'scripts' => [

				],
				'styles'  => [

				],
			],
			'registered' => [
				'scripts' => [

				],
				'styles'  => [

				],
			],
		];
	}

	protected array $firstCollection = [
		[
			'handle' => 'my-collected-enqueued-script-1',
			'src'    => 'my_path/to/my-collected-enqueued-script-1.js',
		],
		[
			'handle' => 'my-collected-enqueued-style-1',
			'src'    => 'my_path/to/my-collected-enqueued-style-1.css',
		],
		[
			'handle' => 'my-collected-enqueued-script-2',
			'src'    => 'my_path/to/my-collected-enqueued-script-2.min.js',
			'deps'   => [ 'jquery' ],
		],
		[
			'handle' => 'my-collected-enqueued-style-2',
			'src'    => 'my_path/to/my-collected-enqueued-style-2.min.css',
			'deps'   => [ 'random-stylesheet-dep' ],
		],
	];

	protected array $secondCollection = [
		[
			'handle' => 'my-collected-enqueued-script-3',
			'src'    => 'my_path/to/my-collected-enqueued-script-3.js',
		],
		[
			'handle' => 'my-collected-enqueued-style-3',
			'src'    => 'my_path/to/my-collected-enqueued-style-3.css',
		],
		[
			'handle' => 'my-collected-enqueued-script-4',
			'src'    => 'my_path/to/my-collected-enqueued-script-4.min.js',
			'deps'   => [ 'jquery' ],
		],
		[
			'handle' => 'my-collected-enqueued-style-4',
			'src'    => 'my_path/to/my-collected-enqueued-style-4.min.css',
			'deps'   => [ 'random-stylesheet-dep' ],
		],
	];

	protected array $thirdCollection = [
		[
			'handle'    => 'my-collected-enqueued-script-5',
			'src'       => 'my_path/to/my-collected-enqueued-script-5.js',
			'ver'       => '2.4.0',
			'in_footer' => false,
		],
		[
			'handle' => 'my-collected-enqueued-style-5',
			'src'    => 'my_path/to/my-collected-enqueued-style-5.css',
			'ver'    => '5.4.0',
			'media'  => 'no',
		],
	];

	public function mergedDepsTestAssertions( $item, $list, $assetType ) {
		if ( isset( $item['deps'] ) ) {
			// Assert if merged dependencies are the same
			$this->assertSame(
				$this->wp_assets[ $list ][ $assetType ][ $item['handle'] ]['deps'],
				array_merge(
					strpos( $item['src'], '.js' ) !== false
						? $this->global_script_deps
						: $this->global_style_deps,
					$item['deps'],
				),
			);
		}
	}

	public function globalParamsTestAssertions( $item, $list, $assetType ) {
		foreach (
			[
				[ 'ver', $item['ver'] ?? $this->assets_version ],
				strpos( $item['src'], '.js' ) !== false
					? [ 'in_footer', $item['in_footer'] ?? $this->in_footer ]
					: [ 'media', $item['media'] ?? $this->media ],
			] as $global ) {
			// Assert if global settings are same
			$this->assertSame( $this->wp_assets[ $list ][ $assetType ][ $item['handle'] ][ $global[0] ], $global[1] );
		}
	}

	public function defaultParamsTestAssertions( $item, $list, $assetType ) {
		foreach ( [ 'handle', 'src' ] as $param ) {
			// Assert if these static params are same
			$this->assertSame(
				$item[ $param ],
				$this->wp_assets[ $list ][ $assetType ][ $item['handle'] ][ $param ]
			);
		}
	}

	/**
	 * Teardown which calls \WP_Mock tearDown
	 *
	 * @return void
	 */
	public function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}