<?php
include_once "func.html.php";

$url = "http://data.soe.com/json/get/eq2/item/?c:limit=100&c:has=typeinfo.growthdescription&c:show=displayname,leveltouse,typeinfo.growthdescription,growth_table,gamelink&c:sort=leveltouse,displayname";

@mkdir("cache");
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
$translate['all'] = 'Ability Modifier';
$translate['sta'] = 'sta';
$translate['agi'] = 'agi';
$translate['int'] = 'int';
$translate['wis'] = 'wis';
$translate['str'] = 'str';
$translate['spelltimecastpct'] = 'Ability Casting Speed';
$translate['basemodifier'] = 'Potency';
$translate['critbonus'] = 'Crit Bonus';
$translate['spelltimereusespellonly'] = 'Spell Reuse Speed';
$translate['dps'] = 'Damage Per Second';
$translate['attackspeed'] = 'Attack Speed';
$translate['spellweapondps'] = 'Spell Weapon Damage Per Second';
$translate['spellweapondoubleattackchance'] = 'Spell Weapon Multi Attack Chance';
$translate['hategainmod'] = 'Hate Gain';
$translate['armormitigationincrease'] = 'Mitigation Increase';
$translate['maxhpperc'] = 'Max Health';
$translate['spelltimereusepct'] = 'Ability Reuse Speed';
$translate['weapondamagebonus'] = 'Weapon Damage Bonus';
$translate['spellweaponattackspeed'] = 'Spell Weapon Attack Speed';
$translate['doubleattackchance'] = 'Multi Attack Chance';
$translate['strikethrough'] = 'Strikethrough';
// $translate[''] = '';


$parsed = array();
$keyHistory = array();
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
			$keyHistory[] = $statKey;

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

$keyHistory = array_unique( $keyHistory );

print "start html\n";
ob_start();

print "<!DOCTYPE html><html>\n";
print "<head>\n";

print '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://u.eq2wire.com/js/eq2u_tools.js"></script>
<link rel="stylesheet" href="adorns.css">
<link rel="stylesheet" href="green.css">
';

print "<title>Green Adornments</title>\n";
print "<style>\n";
print "</style>\n";

print "</head>\n";

print "<body>\n";

print "<table border=1 class='greenTable'>\n";
	print "<tr>\n";
		print "<th>name</th>\n";
		print "<th>lvl</th>\n";
		print "<th>This will grow in power as...</small></td>\n";

		foreach( $keyHistory as $kh ) {
			if( in_array($kh, array('str','agi','int','wis','sta') ) ) {
				$color = 'greenStat';
			} else {
				$color = 'blueStat';
			}
			print "<th class='statHead {$color}'><div class='uptext' title='{$kh}'><div class='uptext_inner'>{$translate[$kh]}</div></div></th>\n";
		}

	print "</tr>\n";


foreach($parsed as $adorn) {
	print "<tr>\n";
		print "<td><a target='_new' href=\"http://u.eq2wire.com/item/index/{$adorn['id']}\">{$adorn['name']}</a></td>\n";

		if( $adorn['level'] < 90 ) {
			print "<td class='under'>";
		} else {
			print "<td>";
		}
			print $adorn['level'] ."</td>\n";

		$growth = str_replace("This will grow in power as ",'',$adorn['growth']);
		if( $growth == "you gain Adventure Experience!" ) {
			print "<td class='commonXP'>";
		} else {
			print "<td>";
		}
		print "<small>... {$growth}</small></td>\n";

		foreach( $keyHistory as $kh ) {
		print "<td class='statBox'>";
			if( !empty($adorn['total'][$kh]) ) {
				print $adorn['total'][$kh];
			}
		print "</td>\n";
		}
	print "</tr>\n";
}
print "</table>\n";

footerLinks();

print "</body>\n";

print "</html>\n";
$html = ob_get_clean();
print "end html\n";

@mkdir("html");
file_put_contents( 'html/green.html', $html );

print "filesize=". filesize('html/green.html') . "\n";
