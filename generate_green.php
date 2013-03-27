<?php
include_once "func.html.php";

$url = "http://data.soe.com/json/get/eq2/item/?c:limit=100&c:has=typeinfo.growthdescription&c:show=displayname,leveltouse,typeinfo.growthdescription,growth_table,gamelink&c:sort=leveltouse,displayname";

if( !file_exists("cache/") ) { mkdir("cache"); }
$cacheFile = 'cache/green.cache';

if( file_exists($cacheFile) ) {
	$json = unserialize( file_get_contents( $cacheFile ) );
} else {
	$json = file_get_contents($url);
	file_put_contents( $cacheFile, serialize($json) );
}

$json = json_decode($json,true);
$adorns = $json['item_list'];
unset($json);

// print_r( $adorns );

$translate = array();
$translate['agi'] = 'agi';
$translate['int'] = 'int';
$translate['str'] = 'str';
$translate['wis'] = 'wis';
$translate['sta'] = 'sta';

$translate['basemodifier'] = 'Potency';
$translate['critbonus'] = 'Crit Bonus';
$translate['strikethrough'] = 'Strikethrough';
$translate['dps'] = 'Damage Per Second';
$translate['armormitigationincrease'] = 'Mitigation Increase';
$translate['attackspeed'] = 'Attack Speed';
$translate['maxhpperc'] = 'Max Health';
$translate['all'] = 'Ability Modifier';
$translate['spelltimecastpct'] = 'Ability Casting Speed';
$translate['spelltimereusespellonly'] = 'Spell Reuse Speed';
$translate['doubleattackchance'] = 'Multi Attack Chance';
$translate['spellweapondoubleattackchance'] = 'SW Multi Attack Chance';
$translate['spellweapondps'] = 'SW Damage Per Second';
$translate['spelltimereusepct'] = 'Ability Reuse Speed';
$translate['weapondamagebonus'] = 'Weapon Damage Bonus';
$translate['spellweaponattackspeed'] = 'SW Attack Speed';
$translate['hategainmod'] = 'Hate Gain';

// $translate[''] = '';

$silkRoot = 'http://silk.ubrfzy.com'; #prod
$image = array();
$image['-']     = "<img class='silk' src='{$silkRoot}/delete.png'>";
$image['?'] = "<img class='silk' src='{$silkRoot}/help.png'>";
$image['check'] = "<img class='silk' src='{$silkRoot}/tick.png'>";


$parsed = array();
$unknownCount = 0;

foreach( $adorns as $adorn ) {

	$current = array();
	$current['id'] = $adorn['id'];
	$current['name'] = $adorn['displayname'];
	$current['level'] = $adorn['leveltouse'];
	$current['growth'] = $adorn['typeinfo']['growthdescription']['growthdescription'];

	$total = array();
	$unknown = false;
	foreach( $adorn['growth_table'] as $levelName => $levelStats ) {
		// print "levelName={$levelName}\n";

		foreach($levelStats as $statKey=>$statVal) {

			if( empty($translate[$statKey]) ) {
				$unknown = true;
				$unknownCount++;
				print "unknown [{$statKey}]\n";
			}
			if( empty($total[$statKey]) ) {
				$total[$statKey] = 0;
			}
			$total[$statKey] += $statVal;
		}
	}
	$current['total'] = $total;

	if( !empty($unknown) ) {
		print "adorn.displayname={$adorn['displayname']}\n";
		print "adorn.gamelink={$adorn['gamelink']}\n";
		print "total({$adorn['displayname']})=\n";
		print_r( $total );
		print "\n";
	}

	$parsed[] = $current;
}

if( $unknownCount ) {
	print "\nHALT! unknownCount={$unknownCount}\n";
	exit;
}

print "start html\n";
ob_start();

print "<!DOCTYPE html><html>\n";
print "<head>\n";

print '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://u.eq2wire.com/js/eq2u_tools.js"></script>
<link rel="stylesheet" href="adorns.css">
<link rel="stylesheet" href="green.css">
<script type="text/javascript" src="green.js"></script>
';

print "<title>Green Adornments</title>\n";
print "<style>\n";
print "</style>\n";

print "</head>\n";

print "<body>\n";
print "<big><b>EQ2 Green Adornments</b></big><br>\n";

print "<table border=1 class='greenTable'>\n";
	print "<tr>\n";
		print "<th>name</th>\n";
		print "<th>lvl</th>\n";
		print "<th>This will grow<br>in power as you...</small></th>\n";
		print "<th></th>\n";

		foreach( $translate as $statKey => $statText ) {
			if( strlen($statText) <= 3 ) {
				$colorClass = 'greenStat';
			} else {
				$colorClass = 'blueStat';
			}
			print "<th class='statHead {$colorClass}'><div class='uptext' title='{$statKey}'><div class='uptext_inner'>{$statText}</div></div></th>\n";
		}
		print "<th></th>\n";

	print "</tr>\n";


foreach($parsed as $adorn) {
	print "<tr class='adornRow'>\n";
		print "<td><a target='_new' href=\"http://u.eq2wire.com/item/index/{$adorn['id']}\">{$adorn['name']}</a></td>\n";

		if( $adorn['level'] < 90 ) {
			print "<td class='levelBox under'>";
		} else {
			print "<td class='levelBox'>";
		}
			print $adorn['level'] ."</td>\n";


		$growth = str_replace("This will grow in power as you",'',$adorn['growth']);
		if( $growth == " gain Adventure Experience!" ) {
			print "<td class='commonXP'>";
		} else {
			print "<td>";
		}
		print "<small>...{$growth}</small></td>\n";
		print "<td>";
		print "<span class='clickable rowMarkHave' title='Mark this row as you HAVE it'>{$image['check']}</span>";
		print "<span class='clickable rowMarkMaybe' title='Mark this row as you CONSIDER it'>{$image['?']}</span>";
		print "</td>\n";

		foreach( $translate as $statKey => $statText ) {
			print "<td class='statBox'>";
			if( !empty($adorn['total'][$statKey]) ) {
				print $adorn['total'][$statKey];
			}
			print "</td>\n";
		}

		print "<td>";
		print "<span class='clickable rowHider' title='hide this row'>{$image['-']}</span>";
		print "</td>\n";
	print "</tr>\n";
}
print "</table>\n";

footerLinks();

print "<hr>\n";
print "Popups provided by <a href='http://u.eq2wire.com'>EQ2U</a>, an <a href='http://eq2wire.com'>EQ2Wire</a> project<br>\n";
print "generated=" . gmdate('r') . "<br>\n";

print "</body>\n";

print "</html>\n";
$html = ob_get_clean();
print "end html\n";

if( !file_exists("html/") ) { mkdir("html"); }
file_put_contents( 'html/green.html', $html );

print "filesize=". filesize('html/green.html') . "\n";

if( !empty($php_errormsg) ) {
	print "php_errormsg= " . $php_errormsg . "\n";
}
