<?php
include_once "master_slots.php";
include_once "func.code.php";
include_once "func.html.php";
include_once "func.curl.php";

$work = array();

if(1) {
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Flickering Adornment of',   ) ), 'html'=>'flickering.html',    'cache'=> 'flickering.dat',    'title'=>'Flickering, L10'); #10
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Glowing Adornment of',      ) ), 'html'=>'glowing.html',       'cache'=> 'glowing.dat',       'title'=>'Glowing, L20'); #20
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Sparkling Adornment of',    ) ), 'html'=>'sparkling.html',     'cache'=> 'sparkling.dat',     'title'=>'Sparkling, L30'); #30
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Glimmering Adornment of',   ) ), 'html'=>'glimmering.html',    'cache'=> 'glimmering.dat',    'title'=>'Glimmering, L40'); #40
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Luminous Adornment of',     ) ), 'html'=>'luminous.html',      'cache'=> 'luminous.dat',      'title'=>'Luminous, L50'); #50
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Scintillating Adornment of',) ), 'html'=>'scintillating.html', 'cache'=> 'scintillating.dat', 'title'=>'Scintillating, L60'); #60
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Smoldering Adornment of',   ) ), 'html'=>'smoldering.html',    'cache'=> 'smoldering.dat',    'title'=>'Smoldering, L70'); #70
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Ethereal Adornment of',     ) ), 'html'=>'ethereal.html',      'cache'=> 'ethereal.dat',      'title'=>'Ethereal, L80'); #80
$work[] = array('color'=>'white', 'search'=>array( array('field'=>"displayname", 'value'=>'/Astral Adornment of',       ) ), 'html'=>'astral.html',        'cache'=> 'astral.dat',        'title'=>'Astral, L90'); #90
}

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

