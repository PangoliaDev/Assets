<?php
declare( strict_types = 1 );

namespace Pangolia\Assets\Abstracts;

use Pangolia\Assets\Traits\EnqueueTrait;
use Pangolia\Assets\Traits\RegisterTrait;

abstract class AbstractManager {
	use EnqueueTrait, RegisterTrait;

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

	/**
	 * @var bool
	 */
	protected bool $condition;

	/** @phpstan-ignore-next-line injection */
	public function __construct( $config ) {
		$this->version = $config['version'] ?? false;
		$this->media = $config['media'] ?? 'all';
		$this->in_footer = $config['in_footer'] ?? false;
		$this->style_deps = $config['style_deps'] ?? [];
		$this->script_deps = $config['script_deps'] ?? [];
		$this->condition = $config['condition'] ?? true;
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
	 * @return $this
	 */
	public function register( $asset = false ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		if ( $asset === false ) {
			$this->loop_assets( $this::REGISTER );
		}

		if ( \is_array( $asset ) ) {
			$this->examine_asset( $this::REGISTER, $asset, $this->get_ext( $asset['src'] ), );
		}

		return $this;
	}

	/**
	 * @param false|array<string, mixed> $asset
	 * @return $this
	 */
	public function enqueue( $asset = false ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		if ( $asset === false ) {
			$this->loop_assets( $this::ENQUEUE, );
		}

		if ( \is_array( $asset ) ) {
			$this->examine_asset( $this::ENQUEUE, $asset, $this->get_ext( $asset['src'] ) );
		}

		return $this;
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
					? $this->enqueue_style(
						$asset['handle'],
						$asset['src'] ?? '',
						$asset['deps'] ?? [],
						$asset['ver'] ?? $this->version,
						$asset['media'] ?? $this->media
					)
					: $this->register_style(
						$asset['handle'],
						$asset['src'],
						$asset['deps'] ?? [],
						$asset['ver'] ?? $this->version,
						$asset['media'] ?? $this->media
					);
				break;
			case 'js':
				$job === $this::ENQUEUE
					? $this->enqueue_script(
						$asset['handle'],
						$asset['src'] ?? '',
						$asset['deps'] ?? [],
						$asset['ver'] ?? $this->version,
						$asset['in_footer'] ?? $this->in_footer
					)
					: $this->register_script(
						$asset['handle'],
						$asset['src'],
						$asset['deps'] ?? [],
						$asset['ver'] ?? $this->version,
						$asset['in_footer'] ?? $this->in_footer
					);
				break;
		}
	}

	/**
	 * @param string[] $handles
	 * @return $this
	 */
	public function remove_styles( array $handles ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		foreach ( $handles as $handle ) {
			$this->remove_style( $handle );
		}

		return $this;
	}

	/**
	 * @param string $handle
	 * @return $this
	 */
	public function remove_style( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		$this->deregister_style( $handle );
		$this->dequeue_style( $handle );

		return $this;
	}

	/**
	 * @param string[] $handles
	 * @return $this
	 */
	public function remove_scripts( array $handles ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		foreach ( $handles as $handle ) {
			$this->remove_script( $handle );
		}

		return $this;
	}

	/**
	 * @param string $handle
	 * @return $this
	 */
	public function remove_script( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		$this->deregister_script( $handle );
		$this->dequeue_script( $handle );

		return $this;
	}

	/**
	 * @return string|bool|null
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @return string[]
	 */
	public function get_script_deps(): array {
		return $this->script_deps;
	}

	/**
	 * @return string[]
	 */
	public function get_style_deps(): array {
		return $this->style_deps;
	}

	/**
	 * @param string $src
	 * @return string
	 */
	protected function get_ext( string $src ): string {
		return \substr( $src, \strrpos( $src, '.' ) + 1 );
	}

	/**
	 * @param bool|callable $condition
	 * @return bool
	 */
	protected function condition_check( $condition ): bool {
		return \is_callable( $condition )
			? \call_user_func( $condition )
			: $condition;
	}
}
