<?php

namespace App\Support\Purifier;

require_once __DIR__.'/../../../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

require_once __DIR__.'/../../../vendor/ezyang/htmlpurifier/library/HTMLPurifier.php';

use HTMLPurifier_Config as Config;

use HTMLPurifier;

class Purify{

	public function makePure($untrustedInput){

		$config = Config::createDefault();

		// configuration goes here:
		$config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
		//$config->set('HTML.Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
		$config->set('URI.AllowedSchemes', array('data' => true));
		$config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');//allow YouTube and Vimeo
		$purifier = new HTMLPurifier($config);

		$pure_html = $purifier->purify($untrustedInput);

		return $pure_html;

	}

}