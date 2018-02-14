<?php

namespace BroOptimizeImage\Classes;

class Meta {

	public $key;

	public $meta_key = '_broptimize';

	public function __construct() {
		add_action( 'BroOptimizeImage__image_optimized', [ $this, 'payload' ], 10, 3 );

	}

	public function payload( $item, $key, $remain ) {

		$array = get_post_meta( intval( $item->post_id ), $this->meta_key, true );

		if ( empty( $array ) ) {
			$array = [];
		}

		array_push( $array, $item->sizes );

		update_post_meta( intval( $item->post_id ), $this->meta_key ,$array);
	}

}