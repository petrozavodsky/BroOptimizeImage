<?php

namespace BroOptimizeImage\Classes;

class AddAttachment {

	public function __construct() {
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'payload' ], 9, 2 );

		add_action( 'delete_attachment', function ( $post_id ) {
			TurnTable::delete( $post_id );
		} );

		add_action( 'BroOptimizeImage__image_optimized_add_promise', [ $this, 'promise' ], 10, 1 );

	}

	public function promise( $attachment_id ) {
		$attachment_id = intval( $attachment_id );
		$metadata      = wp_get_attachment_metadata( $attachment_id );

		$this->payload( $metadata, $attachment_id );
	}

	public function payload( $metadata, $attachment_id ) {

		TurnTable::addItem( $this->files( $attachment_id, $metadata ) );

		return $metadata;
	}

	public function files( $attachment_id, $data ) {
		if ( wp_attachment_is_image( $attachment_id ) ) {

			$name = basename($data['file']);
			$attachment_path = str_replace($name,'', $data['file']);

			$sizes = [];

			if ( $this->extension_helper( $data['file'] ) ) {
				$sizes = [ $data['file'] => $attachment_id ];
			}

			foreach ( $data['sizes'] as $file ) {
				if ( "image/jpeg" == $file['mime-type'] || "image/png" == $file['mime-type'] ) {
					if ( $this->extension_helper( $file['file'] ) ) {
						$sizes[ $attachment_path . $file['file'] ] = $attachment_id;
					}
				}
			}

			if ( 0 >= count( $sizes ) ) {
				return false;
			}

			$sizes = $this->file_duplucate_helper( $sizes, $attachment_id );

			return $sizes;
		}

		return false;
	}


	public function file_duplucate_helper( $sizes, $attachment_id ) {
		global $wpdb;
		$table = $wpdb->prefix . TurnTable::$table_name;

		$old = $wpdb->get_results( "SELECT sizes, post_id FROM {$table} WHERE post_id = '{$attachment_id}' GROUP BY sizes", ARRAY_A );


		if ( 0 >= count( $old ) ) {
			return $sizes;
		}

		$out = [];
		foreach ( $old as $val ) {
			$out[ $val['sizes'] ] = $val['post_id'];
		}

		$sizes = array_diff_assoc( $sizes, $out );

		return $sizes;

	}

	private function extension_helper( $str ) {
		preg_match( '/(.gif)$/', $str, $matches );

		if ( empty( $matches ) ) {
			return true;
		}

		return false;

	}

}

