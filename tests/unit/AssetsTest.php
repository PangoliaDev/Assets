<?php

namespace Pangolia\AssetsTests\Unit;

class AssetsTest extends AssetsTestCase {

	public function testEnqueuedCollection() {
		$this->assets_manager->collect( $this->firstCollection )->enqueue();
		$this->assets_manager->collect( $this->secondCollection )->enqueue();
		$this->assets_manager->collect( $this->thirdCollection )->enqueue();

		foreach ( array_merge(
								$this->firstCollection,
								$this->secondCollection,
								$this->thirdCollection
							) as $collectionItem ) {
			$list = 'enqueued';
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->defaultParamsTestAssertions( $collectionItem, $list, $assetType );
			$this->globalParamsTestAssertions( $collectionItem, $list, $assetType );
			$this->mergedDepsTestAssertions( $collectionItem, $list, $assetType );
		}
	}

	public function testRegisteredCollection() {
		$this->assets_manager->collect( $this->firstCollection )->register();
		$this->assets_manager->collect( $this->secondCollection )->register();
		$this->assets_manager->collect( $this->thirdCollection )->register();
		foreach ( array_merge(
								$this->firstCollection,
								$this->secondCollection,
								$this->thirdCollection
							) as $collectionItem ) {
			$list = 'registered';
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->defaultParamsTestAssertions( $collectionItem, $list, $assetType );
			$this->globalParamsTestAssertions( $collectionItem, $list, $assetType );
			$this->mergedDepsTestAssertions( $collectionItem, $list, $assetType );
		}
	}

	public function testEnqueuedCollectionOnCondition() {
		$is_home = false;
		$is_single = true;

		$this->assets_manager->collect( $this->firstCollection )->enqueue_on( $is_home );
		$this->assets_manager->collect( $this->secondCollection )->enqueue_on( $is_single );
		$this->assets_manager->collect( $this->thirdCollection )->enqueue_on( function () {
			return false;
		} );

		foreach ( array_merge($this->firstCollection, $this->thirdCollection) as $collectionItem ) {
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->assertArrayNotHasKey( $collectionItem['handle'], $this->wp_assets['enqueued'][ $assetType ] );
		}

		foreach ( $this->secondCollection as $collectionItem ) {
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->assertArrayHasKey( $collectionItem['handle'], $this->wp_assets['enqueued'][ $assetType ] );
		}
	}

	public function testRegisteredCollectionOnCondition() {
		$is_home = false;
		$is_single = true;
		$this->assets_manager->collect( $this->firstCollection )->register_on( $is_home );
		$this->assets_manager->collect( $this->secondCollection )->register_on( $is_single );
		$this->assets_manager->collect( $this->thirdCollection )->register_on( function () {
			return false;
		} );

		foreach ( array_merge($this->firstCollection, $this->thirdCollection) as $collectionItem ) {
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->assertArrayNotHasKey( $collectionItem['handle'], $this->wp_assets['registered'][ $assetType ] );
		}

		foreach ( $this->secondCollection as $collectionItem ) {
			$assetType = strpos( $collectionItem['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->assertArrayHasKey( $collectionItem['handle'], $this->wp_assets['registered'][ $assetType ] );
		}
	}

	public function testSingleAsset() {
		$enqueued = [
			[
				'handle' => 'my-enqueued-script-1',
				'src'    => 'my_path/to/my-enqueued-script-1.js',
			],
			[
				'handle' => 'my-enqueued-style-1',
				'src'    => 'my_path/to/my-enqueued-style-1.css',
				'ver'    => '4.5.2',
				'deps'   => [ 'my-random-style-dep' ],
			],
		];

		$registered = [
			[
				'handle' => 'my-registered-script-1',
				'src'    => 'my_path/to/my-registered-script-1.js',
				'deps'   => [ 'my-random-script-dep' ],
			],
			[
				'handle' => 'my-registered-style-1',
				'src'    => 'my_path/to/my-registered-style-1.css',
				'media'  => 'false',
			],
		];

		$this->assets_manager->enqueue( $enqueued[0] );
		$this->assets_manager->enqueue( $enqueued[1] );
		$this->assets_manager->register( $registered[0] );
		$this->assets_manager->register( $registered[1] );

		foreach ( $enqueued as $item ) {
			$assetType = strpos( $item['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->defaultParamsTestAssertions( $item, 'enqueued', $assetType );
			$this->globalParamsTestAssertions( $item, 'enqueued', $assetType );
			$this->mergedDepsTestAssertions( $item, 'enqueued', $assetType );
		}

		foreach ( $registered as $item ) {
			$assetType = strpos( $item['src'], '.js' ) !== false
				? 'scripts'
				: 'styles';

			$this->defaultParamsTestAssertions( $item, 'registered', $assetType );
			$this->globalParamsTestAssertions( $item, 'registered', $assetType );
			$this->mergedDepsTestAssertions( $item, 'registered', $assetType );
		}
	}
}
