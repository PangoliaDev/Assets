<?php
declare( strict_types = 1 );

namespace Pangolia\Assets;

use Pangolia\Assets\Abstracts\AbstractManager;

class ConditionManager extends AbstractManager {

	/**
	 * @return bool
	 */
	public function get_condition(): bool {
		return $this->condition;
	}
}
