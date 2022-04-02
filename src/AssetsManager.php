<?php
declare( strict_types = 1 );

namespace Pangolia\Assets;

class AssetsManager {
	const REGISTER = 'register';
	const ENQUEUE = 'enqueue';
	const STYLES = 'styles';
	const SCRIPTS = 'scripts';

	/**
	 * @var string|bool|null
	 */
	protected $version;

	/**
	 * @var bool
	 */
	protected bool $in_footer;

	/**
	 * @var string
	 */
	protected string $media;

	/**
	 * @var array<int, array>
	 */
	protected array $assets;

	/**
	 * @var string[]
	 */
	protected array $script_deps;

	/**
	 * @var string[]
	 */
	protected array $style_deps;

	/** @phpstan-ignore-next-line */
	public function __construct( $config = [] ) {
		$this->version = $config['version'] ?? false;
		$this->media = $config['media'] ?? 'all';
		$this->in_footer = $config['in_footer'] ?? false;
		$this->style_deps = $config['style_deps'] ?? [];
		$this->script_deps = $config['script_deps'] ?? [];
	}

	/**
	 * @param array<int, array> $assets
	 * @return $this
	 */
	public function collect( array $assets ): self {
		$this->assets = $assets;
		return $this;
	}

	/**
	 * @param false|array<string, mixed> $asset
	 * @return void
	 */
	public function enqueue( $asset = false ) {
		if ( $asset === false ) {
			$this->loop_assets( $this::ENQUEUE, );
		}

		if ( \is_array( $asset ) ) {
			$this->examine_asset( $this::ENQUEUE, $asset, $this->get_ext( $asset['src'] ) );
		}
	}

	/**
	 * @param callable|bool $condition
	 * @return void
	 */
	public function enqueue_on( $condition ) {
		if ( $this->condition_check( $condition ) ) {
			return;
		}

		$this->loop_assets( $this::ENQUEUE, );
	}

	/**
	 * @param false|array<string, mixed> $asset
	 * @return void
	 */
	public function register( $asset = false ) {
		if ( $asset === false ) {
			$this->loop_assets( $this::REGISTER );
		}

		if ( \is_array( $asset ) ) {
			$this->examine_asset( $this::REGISTER, $asset, $this->get_ext( $asset['src'] ), );
		}
	}

	/**
	 * @param callable|bool $condition
	 * @return void
	 */
	public function register_on( $condition ) {
		if ( $this->condition_check( $condition ) ) {
			return;
		}

		$this->loop_assets( $this::REGISTER );
	}

	/**
	 * @param string $job
	 * @return void
	 */
	protected function loop_assets( string $job ) {
		foreach ( $this->assets as $asset ) {
			$this->examine_asset( $job, $asset, $this->get_ext( $asset['src'] ),
			);
		}
	}

	/**
	 * @param string               $job
	 * @param array<string, mixed> $asset
	 * @param string               $ext
	 * @return void
	 */
	protected function examine_asset( string $job, array $asset, string $ext ) {
		switch ( $ext ) {
			case 'css':
				$job === $this::ENQUEUE
					? $this->enqueue_style( $asset )
					: $this->register_style( $asset );
				break;
			case 'js':
				$job === $this::ENQUEUE
					? $this->enqueue_script( $asset )
					: $this->register_script( $asset );
				break;
		}
	}

	/**
	 * @param string|array<string, mixed> $asset
	 * @return void
	 */
	public function enqueue_script( $asset ) {
		\is_string( $asset )
			? \wp_enqueue_script( $asset )
			: \wp_enqueue_script(
			$asset['handle'],
			$asset['src'] ?? '',
			\array_merge(
				$this->script_deps,
				$asset['deps'] ?? []
			),
			$asset['ver'] ?? $this->version,
			$asset['in_footer'] ?? $this->in_footer,
		);
	}

	/**
	 * @param string|array<string, mixed> $asset
	 * @return void
	 */
	public function enqueue_style( $asset ) {
		\is_string( $asset )
			? \wp_enqueue_style( $asset )
			: \wp_enqueue_style(
			$asset['handle'],
			$asset['src'] ?? '',
			\array_merge(
				$this->style_deps,
				$asset['deps'] ?? []
			),
			$asset['ver'] ?? $this->version,
			$asset['media'] ?? $this->media,
		);
	}

	/**
	 * @param array<string, mixed> $asset
	 * @return void
	 */
	public function register_script( array $asset ) {
		\wp_register_script(
			$asset['handle'],
			$asset['src'],
			\array_merge(
				$this->script_deps,
				$asset['deps'] ?? []
			),
			$asset['ver'] ?? $this->version,
			$asset['in_footer'] ?? $this->in_footer,
		);
	}

	/**
	 * @param array<string, mixed> $asset
	 * @return void
	 */
	public function register_style( array $asset ) {
		\wp_register_style(
			$asset['handle'],
			$asset['src'],
			\array_merge(
				$this->style_deps,
				$asset['deps'] ?? []
			),
			$asset['ver'] ?? $this->version,
			$asset['media'] ?? $this->media,
		);
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function remove_styles( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->deregister_style( $handle );
			$this->dequeue_style( $handle );
		}
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function remove_scripts( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->deregister_script( $handle );
			$this->dequeue_script( $handle );
		}
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function deregister_styles( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->deregister_style( $handle );
		}
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function deregister_scripts( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->deregister_script( $handle );
		}
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function dequeue_styles( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->dequeue_style( $handle );
		}
	}

	/**
	 * @param string[] $handles
	 * @return void
	 */
	public function dequeue_scripts( array $handles ) {
		foreach ( $handles as $handle ) {
			$this->dequeue_script( $handle );
		}
	}

	/**
	 * @param string $handle
	 * @return void
	 */
	public function deregister_style( string $handle ) {
		\wp_deregister_style( $handle );
	}

	/**
	 * @param string $handle
	 * @return void
	 */
	public function deregister_script( string $handle ) {
		\wp_deregister_script( $handle );
	}

	/**
	 * @param string $handle
	 * @return void
	 */
	public function dequeue_style( string $handle ) {
		\wp_dequeue_style( $handle );
	}

	/**
	 * @param string $handle
	 * @return void
	 */
	public function dequeue_script( string $handle ) {
		\wp_dequeue_script( $handle );
	}

	/**
	 * @param string $handle
	 * @return bool
	 */
	public function style_is_enqueued( string $handle ): bool {
		return \wp_style_is( $handle );
	}

	/**
	 * @param string $handle
	 * @return bool
	 */
	public function style_is_registered( string $handle ): bool {
		return \wp_style_is( $handle, 'registered' );
	}

	/**
	 * @param string $handle
	 * @return bool
	 */
	public function script_is_enqueued( string $handle ): bool {
		return \wp_script_is( $handle );
	}

	/**
	 * @param string $handle
	 * @return bool
	 */
	public function script_is_registered( string $handle ): bool {
		return \wp_script_is( $handle, 'registered' );
	}

	/**
	 * @param bool|callable $condition
	 * @return bool
	 */
	protected function condition_check( $condition ): bool {
		return \is_callable( $condition ) && \call_user_func( $condition ) === false
			|| \is_bool( $condition ) && $condition === false;
	}

	/**
	 * @param string $src
	 * @return string
	 */
	protected function get_ext( string $src ): string {
		return \substr( $src, \strrpos( $src, '.' ) + 1 );
	}
}
