<?php

function makeHtml( $adornData, $workUnit ) {
	global $slotMap;
	global $groupMap;
	global $groupWidth;

	if( empty($workUnit['html']) ) {
		$workUnit['html'] = time() . '.html';
	}
	
	if( empty($workUnit['color']) ) {
		$workUnit['color'] = 'white';
	}
	
	ob_start();
	print '<!DOCTYPE html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://u.eq2wire.com/js/eq2u_tools.js"></script>
<link rel="stylesheet" href="adorns.css">
<script type="text/javascript" src="adorns.js"></script>
<script type="text/javascript">
var slotText = {};
';
foreach( $slotMap['white'] as $smi => $sm ) {
	print "slotText[{$smi}] = '" . strtolower($sm) . "';\n";
}
print '
</script>
<title>'. $workUnit['title'] .'</title>
</head>
<body>
';

	$qtext = array('l'=>'Lesser','g'=>'Greater','s'=>'Superior');
	$silkRoot = 'http://uber/silk'; #dev
	$silkRoot = 'http://silk.ubrfzy.com'; #prod
	$image = array();
	$image['check'] = "<img class='silk' src='{$silkRoot}/accept.png'>";
	$image['-']     = "<img class='silk' src='{$silkRoot}/delete.png'>";
	$image['reset'] = "<img class='silk' src='{$silkRoot}/arrow_out.png'>";
	$image['only']  = "<img class='silk' src='{$silkRoot}/arrow_in.png'>";
	$image['?']     = "<img class='silk' src='{$silkRoot}/help.png'>";

	print "<table border=1 class='thin'>\n";

	print "<thead>\n";
	print "<tr>\n";
		print "<th colspan='3' rowspan=2>name</th>\n";
		if( $workUnit['color'] != 'white' ) {
			print "<th colspan='1' rowspan=2>links</th>\n";
		} else {
			print "<th colspan='3'>quality</th>\n";
		}
		print "<th colspan='{$groupWidth[$workUnit['color']]['a']}' class='slotGroup' data-group='a' data-original='7'>armor</th>\n";
		print "<th colspan='{$groupWidth[$workUnit['color']]['j']}' class='slotGroup' data-group='j' data-original='7'>jewelery</th>\n";
		print "<th colspan='{$groupWidth[$workUnit['color']]['w']}' class='slotGroup' data-group='w' data-original='3'>weapons</th>\n";
	print "</tr>\n";


	print "<tr>\n";
		if( $workUnit['color'] == 'white' ) {
			foreach( $qtext as $qt ) {
				print "<th class='tiny'>{$qt}</th>\n";
			}
		}
		foreach( $slotMap[$workUnit['color']] as $si=>$slotText ) {
			print "<th class='slotHead tiny' data-slot='{$si}' data-group='{$groupMap[$si]}'>{$slotText}<br>";
			print "<span class='rCol clickable' title='hide this column'>{$image['-']}</span>";
			print "<span class='oCol clickable' title='only show this column'>{$image['only']}</span>";
			print "</th>\n";
		}
	print "</tr>\n";
	unset($qt, $si, $slotText);

	print "</thead>\n";

	/*
	each "adornment" has:
		'root' = string, the base name of the adornment
		'quality' = array(), with keys ('l','g','s'), their value will be false or the id of the item
		'slots' = array(), integer list of slot ids
	*/

	foreach( $adornData as $a ) {

		$dataStrings = '';
		foreach( $a['slots'] as $slotID) {
			$dataStrings .= sprintf(' data-%s="%s"', strtolower( $slotMap['white'][$slotID] ), 1);
		}

		$classExtra = '';
		if( $workUnit['color'] == 'white' ) {
			if( strpos($a['root'], 'of Endurance') // +health
				|| strpos($a['root'], 'of Energy') // +mana
				|| strpos($a['root'], ' Resilience') // lolresists
			) {
				$classExtra .= ' stupid';
			}
			if( strpos($a['root'], 'of Magical Skill') ) { $classExtra .= ' mage priest'; }
			if( strpos($a['root'], 'of Weaponry') ) { $classExtra .= ' fighter scout'; }

			if( strpos($a['root'], 'of Strength') ) { $classExtra .= ' fighter'; }
			if( strpos($a['root'], 'of Wisdom') ) { $classExtra .= ' priest'; }
			if( strpos($a['root'], 'of Agility') ) { $classExtra .= ' scout'; }
			if( strpos($a['root'], 'of Intelligence') ) { $classExtra .= ' mage'; }
		}

		print "<tr class='adornment{$classExtra}'{$dataStrings}>\n";

		# do the left name
		print "<td>{$a['root']}</td>\n";
		print "<td>{$a['level']}</td>\n";
		# minus
		print "<td><span class='clickable rowHider' title='hide this row'>{$image['-']}</span></td>\n";

		if( $workUnit['color'] == 'white' ) {
			# do the quality cells
			foreach( $a['quality'] as $qi=>$has ) {
				$inner = ''; # reset
				$class = ''; # reset
				$qt = $qtext[$qi]; # copy this, since we use it a few times

				#mark the left one with a visual bar
				// $class .= ($qi=='l')?' bl':'';

				# we can haz?
				if( $has ) {
					# we haz!

					#rebuild the full item name
					$fullname = "{$a['root']} ({$qt})";
					# build the url
					$wireItemURL = "http://u.eq2wire.com/i/" . urlencode($fullname );
					$wireItemURL = "http://u.eq2wire.com/item/index/" . $has;

					# fully assemble the cell contents
					$inner = "<a target='_blank' href=\"{$wireItemURL}\">{$qt}</a>";

					$class .= ' ' . strtolower($qt);
				}
				$class = trim($class);

				# spit out what ever we build
				print "<td class='{$class}'>{$inner}</td>\n";
			}
		} else {
			$wireItemURL = "http://u.eq2wire.com/item/index/" . $a['quality']['*'];
			$inner = "<a target='_blank' href=\"{$wireItemURL}\">Link</a>";
			print "<td>{$inner}</td>\n";
		}

		# do the slots
		#   we make a cell for every slot (using slotMap)
		#   and conditionally color+image if this adorn can be placed there
		foreach($slotMap[$workUnit['color']] as $slotIndex=>$mn) {
			#they all are marked with this
			$c = 'slot';
			# reset contents
			$inner = '';

			# conditionally mark some of them with leftbar
			if(    $slotIndex==2  /* left of head    */
				or $slotIndex==19 /* left of cloak   */
				or $slotIndex==0  /* left of primary */
				) {
				$c .= ' bl';
			}
			if( in_array($slotIndex, $a['slots']) ) {
				$c .= ' pos';
				$inner = "<span class='slotPos clickable' data-mark='?' title='possible placement'>" . $image['?'] . "</span>";
			}
			print "<td class='{$c}' data-slot='{$slotIndex}'>{$inner}</td>\n";
		}
		print "</tr>\n";
	}
	print "</table>\n";
	print "<button class='rowReset clickable'>" . $image['reset'] . " unhide all hidden rows</button>\n";
	print "<button class='colReset clickable'>" . $image['reset'] . " unhide all hidden columns</button><br>\n";

	footerLinks();

	print "<hr>\n";
	print "Popups provided by <a href='http://u.eq2wire.com'>EQ2U</a>, an <a href='http://eq2wire.com'>EQ2Wire</a> project<br>\n";
	print "generated=" . gmdate('r') . "<br>\n";
	print "</body>\n";
	print "</html>\n";

	$htmlDir = 'html/';
	@mkdir( $htmlDir );

	file_put_contents( $htmlDir . $workUnit['html'], ob_get_clean() );
	print "html written\n";
}

function footerLinks() {
?>
Links:<br>
White <ul class='inline'>
<li class='nav'><a href="astral">Astrals (L90)</a></li>
<li class='nav'><a href="ethereal">Ethereals (L80)</a></li>
<li class='nav'><a href="smoldering">Smoldering (L70)</a></li>
<li class='nav'><a href="scintillating">Scintillating (L60)</a></li>
<li class='nav'><a href="luminous">Luminous (L50)</a></li>
<li class='nav'><a href="glimmering">Glimmering (L40)</a></li>
<li class='nav'><a href="sparkling">Sparkling (L30)</a></li>
<li class='nav'><a href="glowing">Glowing (L20)</a></li>
<li class='nav'><a href="flickering">Flickering (L10)</a></li>
</ul><br>
Yellow <ul class='inline'>
<li class='nav'><a href="astral_yellow">Astrals (L90)</a></li>
<li class='nav'><a href="ethereal_yellow">Ethereals (L80)</a></li>
</ul><br>
Red <ul class='inline'>
<li class='nav'><a href="astral_red">Astrals (L90)</a></li>
</ul><br>
Green <ul class='inline'>
<li class='nav'><a href="green">All Levels</a></li>
</ul><br>
<?php
}
