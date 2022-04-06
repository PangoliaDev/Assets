<?php
declare( strict_types = 1 );

namespace Pangolia\Assets\Traits;

trait RegisterTrait {

	/**
	 * @param string           $handle
	 * @param string           $src
	 * @param string[]         $deps
	 * @param string|bool|null $ver
	 * @param bool|null        $in_footer
	 * @return $this
	 */
	public function register_script(
		string $handle,
		string $src,
		array $deps = [],
		$ver = null,
		$in_footer = null
	): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_register_script(
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
	public function register_style(
		string $handle,
		string $src = '',
		array $deps = [],
		$ver = null,
		$media = null
	): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_register_style(
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
	 * @param string $handle
	 * @return $this
	 */
	public function deregister_style( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_deregister_style( $handle );

		return $this;
	}

	/**
	 * @param string $handle
	 * @return $this
	 */
	public function deregister_script( string $handle ): self {
		if ( $this->condition === false ) {
			return $this;
		}

		\wp_deregister_script( $handle );

		return $this;
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
	public function script_is_registered( string $handle ): bool {
		return \wp_script_is( $handle, 'registered' );
	}
}
