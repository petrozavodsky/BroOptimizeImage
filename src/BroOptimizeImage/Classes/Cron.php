<?php

namespace BroOptimizeImage\Classes;


class Cron {

	public function __construct() {

		self::interval();

		add_action( 'BroOptimizeImage__schedule_commonly', [ __CLASS__, 'task' ] );
	}

	public static function interval() {
		add_filter( 'cron_schedules', function ( $schedules ) {
			$schedules['BroOptimizeImage__schedule_commonly'] = [
				'interval' => MINUTE_IN_SECONDS * 3.3,
				'display'  => '~ 3.1 min.'
			];

			return $schedules;
		} );
	}

	public static function task() {

		$optimize = new Optimize();
		$optimize->selectActiveKey();
		$optimize->process();

	}

}