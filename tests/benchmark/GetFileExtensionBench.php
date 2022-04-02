<?php

namespace Pangolia\AssetsTests\Benchmark;

use Pangolia\Assets\AssetsManager;

class GetFileExtensionBench {

	protected string $file = 'my_path/to/my/stylesheet.css';

	/**
	 * @Revs(10000)
	 */
	public function benchByPathInfo() {
		return \pathinfo( $this->file )['extension'];
	}

	/**
	 * @Revs(10000)
	 */
	public function benchByPathInfoConstant() {
		return \pathinfo( $this->file, PATHINFO_EXTENSION );
	}

	/**
	 * @Revs(10000)
	 */
	public function benchBySubstrStrrpos() {
		return \substr( $this->file, \strrpos( $this->file, '.' ) + 1 );
	}

	/**
	 * @Revs(10000)
	 */
	public function benchBySubstrStrrchr() {
		return substr(strrchr($this->file, "."), 1);
	}

	/**
	 * @Revs(10000)
	 */
	public function benchByStrrposBool() {
		return \strrpos( $this->file, '.js' ) !== false || \strrpos( $this->file, '.css' ) !== false;
	}

	/**
	 * @Revs(10000)
	 */
	public function benchByPregMatchBool() {
		return \preg_match("/\.(js)$/", $this->file) || \preg_match("/\.(css)$/", $this->file);
	}
}