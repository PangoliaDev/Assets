<?php
declare( strict_types = 1 );

namespace Pangolia\Assets\Traits;

trait EnqueueTrait {

	/**
	 * @param string           $handle
	 * @param string           $src
	 * @param string[]         $deps
	 * @param string|bool|null $ver
	 * @param bool|null        $in_footer
	 * @return $this
	 */
	public function enqueue_script(
		string $handle,
		string $src = '',
		array $deps = [],
		$ver = null,
		$in_footer = null
	): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_enqueue_script(
			$handle,
			$src,
			\array_merge(
				$this->script_deps,
				$deps
			),
			$ver ?? $this->version,
			$in_footer ?? $this->in_footer,
		);

		return $this;
	}

	/**
	 * @param string           $handle
	 * @param string           $src
	 * @param string[]         $deps
	 * @param string|bool|null $ver
	 * @param string|null      $media
	 * @return $this
	 */
	public function enqueue_style(
		string $handle,
		string $src = '',
		array $deps = [],
		$ver = null,
		$media = null
	): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_enqueue_style(
			$handle,
			$src,
			\array_merge(
				$this->style_deps,
				$deps
			),
			$ver ?? $this->version,
			/** @phpstan-ignore-next-line will always be a string */
			$media ?? $this->media,
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
