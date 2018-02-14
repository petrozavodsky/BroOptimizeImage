<?php


namespace BroOptimizeImage\Classes;

class TurnTable {
	public static $table_name = 'image_optimize_turn';

	public static function getItem() {
		global $wpdb;

		$limit = \BroOptimizeImage::$batch;

		$table = $wpdb->prefix . self::$table_name;
		$out   = $wpdb->get_results( "SELECT sizes, post_id, ID FROM {$table} WHERE status = '1' ORDER BY ID DESC LIMIT {$limit};" );

		if ( empty( $out ) ) {
			return false;
		}

		return $out;
	}

	public static function addItem( $data ) {
		global $wpdb;

		$table = $wpdb->prefix . self::$table_name;


		$implode_data = function ( $data ) {
			$res   = '';
			$count = count( $data );
			$i     = 1;

			if ( empty( $data ) ) {
				return false;
			}


			foreach ( $data as $key => $val ) {
				$res .= "(1, {$val}, '{$key}' )";

				if ( $i !== $count ) {
					$res .= ',';
				}
				$i ++;
			}

			return $res;
		};


		$vals = $implode_data( $data );

		if ( false === $vals ) {
			return false;
		}


		return $wpdb->query( $wpdb->prepare(
			"
			INSERT INTO {$table}
			 ( status, post_id, sizes) 
			VALUES {$vals};
			",
			[ '%d', '%s' ]
		) );
	}

	public static function changeStatusItem( $ID, $status = 0 ) {
		global $wpdb;

		$table = $wpdb->prefix . self::$table_name;

		$out = $wpdb->update(
			$table,
			[ 'status' => $status ],
			[ 'ID' => $ID ],
			[ '%d' ],
			[ '%d' ]
		);
		if ( 0 === $out ) {
			return false;
		}

		return $out;
	}

	public static function delete( $post_id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		return $wpdb->delete(
			"{$table}",
			[ 'post_id' => $post_id ],
			[ '%d' ]
		);
	}

	public static function clean() {
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		return $wpdb->query( "
		DELETE FROM {$table}
		WHERE status = 0;
		" );
	}


}