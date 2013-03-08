$(document).ready( function() {
	// console.log('ready()!');
	$(document).on('click', '.rowHider', rowHider );
	$(document).on('click', '.rowReset', rowReset );
	$(document).on('click', '.slotHead .rCol', colHider );
	$(document).on('click', '.slotHead .oCol', colOnly );
	$(document).on('click', '.colReset', colReset );
	$(document).on('click', '.slotPos', slotPos );

} );

function rowHider() {
	// console.log('you clicked a hider!');
	var par = $(this).parents("tr");
	// console.log( par );
	$(par).hide();
	$(".rowReset").fadeIn('fast');
}

function rowReset() {
	// console.log('you clicked a reset!');
	$('tr').fadeIn('fast');
}

function colHider() {
	// console.log( 'colHider()!' );

	var par = $( this ).parent();
	var slot = par.attr('data-slot');

	$( ".slotHead[data-slot="+slot+"]").hide();
	$( ".slot[data-slot="+slot+"]").hide();

	var group = par.attr('data-group');
	var curspan = $( "th.slotGroup[data-group="+group+"]").attr('colspan');
	var newspan = parseInt(curspan,10) - 1;

	if( newspan > 0 ) {
		$( "th.slotGroup[data-group="+group+"]").attr('colspan', newspan);
	} else {
		$( "th.slotGroup[data-group="+group+"]").hide();
	}
	$(".colReset").fadeIn();
}

function colOnly () {
	// console.log( 'colOnly()!' );

	var par = $( this ).parent();
	var slot = par.attr('data-slot');

	$( ".slotHead[data-slot!="+slot+"]").hide();
	$( ".slot[data-slot!="+slot+"]").hide();

	var group = par.attr('data-group');

	$( "th.slotGroup[data-group="+group+"]").attr('colspan', 1);
	$( "th.slotGroup[data-group!="+group+"]").hide();
	$(".colReset").fadeIn();
}

function colReset() {
	$("th.slotGroup").show();
	$("th.slotHead").show();
	$("td.slot").show();
	$(".slotGroup").each( function() {
		var o = $(this).attr('data-original');
		// console.log( o );
		// console.log( $(this) );
		$(this).attr('colspan', o);
	} );

}

function slotPos() {
	var mark = $(this).attr('data-mark');
	var icon = $(this).find('img');

	if( mark == '?' ) {
		$(icon).attr('src', 'http://silk.ubrfzy.com/accept.png');
		$(this).attr('data-mark','check').attr('title','');

	}
	if( mark == 'check' ) {
		$(icon).attr('src', 'http://silk.ubrfzy.com/help.png');
		$(this).attr('data-mark','?').attr('title','possible placement');
	}
}
