<?php

function curl_get( $url ) {
	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// grab URL and pass it to the browser
	$ret = curl_exec($ch);

	// close cURL resource, and free up system resources
	curl_close($ch);
	
	return $ret;
}
