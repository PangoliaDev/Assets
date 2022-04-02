<?php
declare( strict_types = 1 );

namespace Pangolia\Assets\Traits;

trait RegisterTrait {

	/**
	 * @param array<string, mixed> $asset
	 * @return $this
	 */
	public function register_style( array $asset ): self {
		if ( $this->condition === false ) {
			return $this;
		}

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

		return $this;
	}

	/**
	 * @param array<string, mixed> $asset
	 * @return $this
	 */
	public function register_script( array $asset ): self {
		if ( $this->condition === false ) {
			return $this;
		}

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