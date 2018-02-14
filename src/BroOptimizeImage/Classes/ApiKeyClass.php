<?php

namespace BroOptimizeImage\Classes;


class ApiKeyClass {

	public static $remain_default = 500;

	public $remain = 500;

	public $key;

	public $api_keys_option_field;

	public $options = [];

	public $keys_list = [];

	public function __construct() {
		$this->api_keys_option_field = OptionPage::$settings_prefix . 'api_keys';

		$this->options = get_option(
			$this->api_keys_option_field,
			[
				'keys' => false
			]
		);

		$this->keys_list = $this->extract_keys();

		$this->key    = key( $this->keys_list );
		$this->remain = array_shift( $this->keys_list );

	}

	public function getKey() {
		return $this->key;
	}

	public function getRemain() {
		return $this->remain;
	}

	public function extract_keys() {

		if ( ! $this->options['keys'] ) {
			return $this->options['keys'];
		}

		$array = explode( PHP_EOL, trim( $this->options['keys'] ) );
		$array = array_map( 'trim', $array );

		$out = [];
		foreach ( $array as $val ) {
			preg_match( '/:\d*$/', $val, $matches );
			$key         = str_replace( $matches, '', $val );
			$remains     = str_replace( ':', '', array_shift( $matches ) );
			$out[ $key ] = (int) $remains;
		}

		return $out;
	}

	public function sort_keys(){
		arsort( $this->keys_list  );
	}

	public function build_keys() {
		$res = '';

		$this->keys_list[ $this->key ] = $this->remain;

		$this->sort_keys();

		if ( is_array( $this->keys_list ) && 0 < count( $this->keys_list ) ) {
			foreach ( $this->keys_list as $k => $v ) {
				$res .= $k . ':' . $v . PHP_EOL;
			}
		}
		$this->options['keys'] = $res;

		update_option( $this->api_keys_option_field, $this->options ,  false);

		return $res;

	}

}