<?php
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
$translate['spellweapondps'] = 'Spell&nbsp;Weapon Damage Per Second';
$translate['spellweapondoubleattackchance'] = 'Spell&nbsp;Weapon Multi Attack Chance';
$translate['hategainmod'] = 'Hate Gain';
$translate['armormitigationincrease'] = 'Mitigation Increase';
$translate['maxhpperc'] = 'Max Health';
$translate['spelltimereusepct'] = 'Ability Reuse Speed';
$translate['weapondamagebonus'] = 'Weapon Damage Bonus';
$translate['spellweaponattackspeed'] = 'Spell&nbsp;Weapon Attack Speed';
$translate['doubleattackchance'] = 'Multi Attack Chance';
$translate['strikethrough'] = 'Strike&#8203through';
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

print "\n\n";
if( $unknownCount ) {
	print "unknownCount={$unknownCount}\n";
	exit;
}

$keyHistory = array_unique( $keyHistory );

ob_start();

print "<!DOCTYPE html><html>\n";
print "<head>\n";

print "<style>\n";
print
".uptext {
    display: block;
	-webkit-transform: rotate(-90deg);
	-moz-transform: rotate(-90deg);
	filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}

.statHead {
	width: 2.5em;
	font-size: 80%;
}
.statBox {
	width: 2em;
}
";
print "</style>\n";

print "</head>\n";

print "<body>\n";

print "<table border=1>\n";
	print "<tr>\n";
		print "<th>name/<br>\n";
		print "id</th>\n";
		print "<th>level</th>\n";
		print "<th>This will grow in power as...</small></td>\n";

		foreach( $keyHistory as $kh ) {
		print "<th class='statHead'><span class='uptext' title='{$kh}'>{$translate[$kh]}</span></th>\n";
		}

	print "</tr>\n";


foreach($parsed as $adorn) {
	print "<tr>\n";
		print "<td>{$adorn['level']}</td>\n";
		print "<td><small>... ". str_replace("This will grow in power as ",'',$adorn['growth']) ."</small></td>\n";
		print "<td><a target='_new' href=\"http://u.eq2wire.com/item/index/{$adorn['id']}\">{$adorn['name']}</a><br/>\n";
		print "<small>{$adorn['id']}</small></td>\n";

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
print "</body>\n";

print "</html>\n";

@mkdir("html");
file_put_contents( 'html/green.html', ob_get_clean() );

