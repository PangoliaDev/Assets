<?php
declare( strict_types = 1 );

namespace Pangolia\Assets\Traits;

trait EnqueueTrait {

	/**
	 * @param string|array<string, mixed> $asset
	 * @return $this
	 */
	public function enqueue_script( $asset ): self {
		if ( $this->condition === false ) {
			return $this;
		}

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

		return $this;
	}

	/**
	 * @param string|array<string, mixed> $asset
	 * @return $this
	 */
	public function enqueue_style( $asset ): self {
		if ( $this->condition === false ) {
			return $this;
		}

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

		return $this;
	}

	/**
	 * @param string[] $handles
	 * @return $this
	 */
	public function dequeue_styles( array $handles ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		foreach ( $handles as $handle ) {
			$this->dequeue_style( $handle );
		}

		return $this;
	}

	/**
	 * @param string[] $handles
	 * @return $this
	 */
	public function dequeue_scripts( array $handles ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		foreach ( $handles as $handle ) {
			$this->dequeue_script( $handle );
		}

		return $this;
	}

	/**
	 * @param string $handle
	 * @return $this
	 */
	public function dequeue_style( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_dequeue_style( $handle );

		return $this;
	}

	/**
	 * @param string $handle
	 * @return $this
	 */
	public function dequeue_script( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_dequeue_script( $handle );

		return $this;
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
	public function script_is_enqueued( string $handle ): bool {
		return \wp_script_is( $handle );
	}
}