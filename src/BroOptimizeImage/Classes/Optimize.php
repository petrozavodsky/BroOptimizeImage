<?php

namespace BroOptimizeImage\Classes;


use Tinify\Source;
use Tinify\Tinify;

class Optimize {

	public $dir_info;
	public $path;
	public $key;
	public $key_class;

	public function __construct() {
		$this->dir_info  = wp_get_upload_dir();
		$this->path      = $this->dir_info['basedir'] . "/" . $this->path;
		$this->key_class = new ApiKeyClass();
		$this->key       = $this->key_class->key;
	}

	public function selectActiveKey() {
		$this->key_class = new ApiKeyClass();
		$this->key       = $this->key_class->key;
	}

	public function optimize( $in, $out ) {
		$source = Source::fromFile(
			$in
		);

		return $source->toFile(
			$out
		);
	}

	public function process() {

		Tinify::setKey( $this->key );
		$items = TurnTable::getItem();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {

				$this->optimize(
					$this->path . $item->sizes,
					$this->path . $item->sizes
				);


				try {
					TurnTable::changeStatusItem( $item->ID );

					$compressionsThisMonth = \Tinify\compressionCount();
					do_action( 'BroOptimizeImage__image_optimized', $item, $this->key, $compressionsThisMonth );

					$this->rebuild_keys( ApiKeyClass::$remain_default - $compressionsThisMonth );

				} catch (\Exception $e) {

					$this->rebuild_keys( ApiKeyClass::$remain_default - $compressionsThisMonth );
					wp_die($e->getMessage());
				}

			}
		}

	}

	public function rebuild_keys( $remain = false ) {

		if ( false !== $remain ) {

			if ( 0 >= $remain ) {
				$remain = 0;
			}

			$this->key_class->remain = $remain;
		}

		$this->key_class->build_keys();
	}

}