<?php
function getData($searchTerms, $cacheFile) {
	$params = array();
	$params['c:limit'] = '1000';
	// $params['c:limit'] = '10';
	$params['c:show'] = 'typeinfo.slot_list,leveltouse,typeinfo.color';
	$params['c:sort'] = 'displayname';
	$params['typeinfo.name'] = 'adornment';

	// print_r($params); print "\n";

	$sparams = array();
	foreach( $searchTerms as $st ) {
		$sparams[] = sprintf("%s%s%s",  $st['field'], (empty($st['glue'])?'=':$st['glue']), str_replace(' ','+',$st['value']) );
	}
	// print_r($sparams);
	$ss = implode("&", $sparams);
	// print_r($ss); print "\n";

	$cacheDir = 'cache/';
	@mkdir( $cacheDir );

	if( file_exists($cacheDir . $cacheFile) && 1) {
		print "cache detected, loading\n";
		$json = unserialize( file_get_contents( $cacheDir . $cacheFile ) );
	} else {
		print "cache MISS, fetching from web\n";
		// var_dump( $params );

		$url = 'http://data.soe.com/s:uberfuzzy/json/get/eq2/item/?'. http_build_query($params) . '&' . $ss;
		// $url = str_replace('%3A', ':', $url);
		// $url = str_replace('%2C', ',', $url);
		print "url=" . $url . "\n";
		// var_dump( $url );

		$ret = curl_get( $url );
		$json = json_decode( $ret );
		if( $json == false ) { return false; }
		// var_dump( $json );

		$json = $json->item_list;
		if( empty($json) ) { return false; }

		file_put_contents( $cacheDir . $cacheFile, serialize( $json ) );
	}

	return $json;
}

function json2adorns( $json ) {
	$adorns = array();

	foreach( $json as $item ) {
		print "->displayname=" . $item->displayname . "\n";
		// print_r($item);

		if( $item->typeinfo->color == 'white' ) {
			list( $root, $grade) = explode("(", rtrim($item->displayname,")") );
			$root = trim($root);
			$gradeKey = strtolower( substr($grade,0,1) );
		}
		else {
			$root = $item->displayname;
			$gradeKey = '*';
		}

		$slot_list = array();
		if( empty($item->typeinfo->slot_list) ) { print "INVALID ADORNMENT ^^^^^ ^^^^^ ^^^^^ ^^^^^ ^^^^^\n"; continue; }

		foreach( $item->typeinfo->slot_list as $slot ) {
			$slot_list[] = (int)$slot->id;
		}
		// print "slots=[" . implode(",", $slot_list) . "]\n"; #DEBUG

		if( array_key_exists($root, $adorns) ) {
			# we already have some existing data about this adornment (in a diff form)
			# we dont need to set [root]
			# we DO need to set [quality][key]
			# we dont need to set [slots]
			$adorns[ $root ]['quality'][$gradeKey] = $item->id;
		} else {
			# this is new info
			$adorns[$root] = array(
				'root' => $root,
				'quality' => array(),
				'slots' => $slot_list,
				'level' => $item->leveltouse,
				);

			if( $item->typeinfo->color == 'white' ) {
				$adorns[$root]['quality'] = array(
						'l'=> ($gradeKey=='l'?$item->id:false),
						'g'=> ($gradeKey=='g'?$item->id:false),
						's'=> ($gradeKey=='s'?$item->id:false)
					);
			} else {
				$adorns[$root]['quality'] = array(
						'*'=> ($gradeKey=='*'?$item->id:false)
					);
			}
		}
	}
	return $adorns;
}


