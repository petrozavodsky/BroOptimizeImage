<?php

namespace BroOptimizeImage\Classes;


class JpegQuality {


	public static function quality(){
		add_filter( 'jpeg_quality', function () {
			return 92;
		},11 );
	}
}