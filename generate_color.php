<?php
include_once "master_slots.php";
include_once "func.code.php";
include_once "func.html.php";
include_once "func.curl.php";

$work = array();

$work[] = array(
	'color'=> 'yellow',
	'search'=> array(
		array('field'=>"leveltouse", 'glue'=>'=]', 'value'=>80),
		array('field'=>"leveltouse", 'glue'=>'=<', 'value'=>89),
		array('field'=>"typeinfo.color", 'value'=>'yellow'),
	),
	'html'=> 'ethereal_yellow.html',
	'cache'=> 'ethereal_yellow.dat',
); #80


$work[] = array(
	'color'=> 'yellow',
	'search'=>array(
		array('field'=>"leveltouse", 'glue'=>'=]', 'value'=>90),
		array('field'=>"leveltouse", 'glue'=>'=<', 'value'=>99),
		array('field'=>"typeinfo.color", 'value'=>'yellow'),
	),
	'html'=> 'astral_yellow.html',
	'cache'=> 'astral_yellow.dat',
); #90

$work[] = array(
	'color'=> 'yellow',
	'search'=>array(
		array('field'=>"leveltouse", 'glue'=>'=]', 'value'=>90),
		array('field'=>"leveltouse", 'glue'=>'=<', 'value'=>99),
		array('field'=>"typeinfo.color", 'value'=>'red'),
	),
	'html'=> 'astral_red.html',
	'cache'=> 'astral_red.dat',
); #90


# --------------------------------------------------------------------------------
foreach( $work as $unit ) {
	print "=== loop ======\n";
	print "fetching data\n";
	$json = getData($unit['search'], $unit['cache']);
	// print_r($json);
	if( $json == false ) {
		print "json was false, skipping json2adorn stage\n";
		continue;
	}


	print "data ready, looping to process\n";
	$adorns = json2adorns( $json );
	
	if( $adorns == false ) {
		print "adorns was false, skipping html stage\n";
		continue;
	}

	print "start html\n";
	makeHtml( $adorns, $unit );
}

// ob_start();
// print_r($adorns);
// file_put_contents( 'debug.txt', ob_get_clean() );

