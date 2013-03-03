<?php
$slotMap = array('white');
$slotMap['white'][2] = 'Head';
$slotMap['white'][4] = 'Shoulders';
$slotMap['white'][3] = 'Chest';
$slotMap['white'][5] = 'Forearms';
$slotMap['white'][6] = 'Hands';
$slotMap['white'][7] = 'Legs';
$slotMap['white'][8] = 'Feet';

$slotMap['white'][19] = 'Cloak';
$slotMap['white'][20] = 'Charm';
$slotMap['white'][11] = 'Ear';
$slotMap['white'][13] = 'Neck';
$slotMap['white'][9] = 'Finger';
$slotMap['white'][14] = 'Wrist';
$slotMap['white'][18] = 'Waist';

$slotMap['white'][0] = 'Primary';
$slotMap['white'][1] = 'Secondary';
$slotMap['white'][16] = 'Ranged';

$slotMap['yellow'] = $slotMap['white'];
unset($slotMap['yellow'][11]);
unset($slotMap['yellow'][13]);
unset($slotMap['yellow'][9]);
unset($slotMap['yellow'][14]);

$slotMap['red'] = $slotMap['yellow'];


$groupMap = array();
$groupMap[2] = 'a';
$groupMap[4] = 'a';
$groupMap[3] = 'a';
$groupMap[5] = 'a';
$groupMap[6] = 'a';
$groupMap[7] = 'a';
$groupMap[8] = 'a';

$groupMap[19] = 'j';
$groupMap[20] = 'j';
$groupMap[11] = 'j';
$groupMap[13] = 'j';
$groupMap[9]  = 'j';
$groupMap[14] = 'j';
$groupMap[18] = 'j';

$groupMap[0]  = 'w';
$groupMap[1]  = 'w';
$groupMap[16] = 'w';

$groupWidth = array();
$groupWidth['white']  = array('a'=>7, 'j'=>7, 'w'=>3);
$groupWidth['yellow'] = array('a'=>7, 'j'=>3, 'w'=>3);
$groupWidth['red']    = array('a'=>7, 'j'=>3, 'w'=>3);
