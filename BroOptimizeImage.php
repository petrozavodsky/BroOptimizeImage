<?php

/*
Plugin Name: Bro Optimize Image
Plugin URI: http://alkoweb.ru
Author: Petrozavodsky
Author URI: http://alkoweb.ru
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( "vendor/autoload.php" );
require_once( "includes/Autoloader.php" );


use BroOptimizeImage\Autoloader;

new Autoloader( __FILE__, 'BroOptimizeImage' );


use BroOptimizeImage\Base\Wrap;
use BroOptimizeImage\Classes\AddAttachment;
use BroOptimizeImage\Classes\Cron;
use BroOptimizeImage\Classes\JpegQuality;
use BroOptimizeImage\Classes\Meta;
use BroOptimizeImage\Classes\OptionPage;
use BroOptimizeImage\Classes\TurnTable;

class BroOptimizeImage extends Wrap {
	public $version = '1.0.1';
	public static $textdomine;
	public static $batch = 11;

	function __construct() {
		self::$batch = apply_filters( 'BroOptimizeImage__stack', 11 );

		self::$textdomine = $this->setTextdomain();

		JpegQuality::quality();

		new Meta();

		new AddAttachment();

		new OptionPage();

		new Cron();

	}

	public static function install() {
		global $wpdb;

		\BroOptimizeImage\Classes\Cron::interval();

		if ( ! wp_next_scheduled( 'BroOptimizeImage__schedule_commonly' ) ) {
			wp_schedule_event(

				time(),
				'BroOptimizeImage__schedule_commonly',
				'BroOptimizeImage__schedule_commonly'
			);
		}

		if ( ! wp_next_scheduled( 'BroOptimizeImage__schedule_infrequently' ) ) {
			wp_schedule_event(
				time(),
				'BroOptimizeImage__schedule_infrequently',
				'BroOptimizeImage__schedule_infrequently'
			);
		}

		$table_name = $wpdb->get_blog_prefix() . TurnTable::$table_name;
		if ( 1 != $wpdb->query( "show tables like {$table_name}" ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$sql = "
			CREATE TABLE {$table_name} (
		    ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    post_id bigint(20) NOT NULL, 
		    status int(1) NOT NULL, 
		    sizes longtext,
			KEY status (status)
			) {$charset_collate};";

			dbDelta( trim( $sql ) );
		}

	}

	public static function uninstall() {
		wp_clear_scheduled_hook( 'BroOptimizeImage__schedule_commonly' );
		wp_clear_scheduled_hook( 'BroOptimizeImage__schedule_infrequently' );
	}

}

register_activation_hook( __FILE__, [ 'BroOptimizeImage', 'install' ] );
register_deactivation_hook( __FILE__, [ 'BroOptimizeImage', 'uninstall' ] );

function BroOptimizeImage__init() {
	new BroOptimizeImage();
}

add_action( 'plugins_loaded', 'BroOptimizeImage__init', 30 );