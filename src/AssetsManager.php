<?php
declare( strict_types = 1 );

namespace Pangolia\Assets;

use Pangolia\Assets\Abstracts\AbstractManager;

class AssetsManager extends AbstractManager {

	/**
	 * @param bool|callable $condition
	 * @return \Pangolia\Assets\ConditionManager
	 */
	public function on( $condition ): ConditionManager {
		return new ConditionManager( [
			'condition'   => $this->condition_check( $condition ),
			'version'     => $this->version,
			'media'       => $this->media,
			'in_footer'   => $this->in_footer,
			'style_deps'  => $this->style_deps,
			'script_deps' => $this->script_deps,
		] );
	}

	/**
	 * @param callable|bool $condition
	 * @return $this
	 */
	public function register_on( $condition ): self {
		if ( $this->condition === false || $this->condition_check( $condition ) === false ) {
			return $this;
		}

		$this->loop_assets( $this::REGISTER );

		return $this;
	}

	/**
	 * @param callable|bool $condition
	 * @return $this
	 */
	public function enqueue_on( $condition ): self {
		if ( $this->condition === false || $this->condition_check( $condition ) === false ) {
			return $this;
		}

		$this->loop_assets( $this::ENQUEUE, );

		return $this;
	}
}
