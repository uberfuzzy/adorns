$(document).ready( function() {
	$(document).on('click', '.rowHider', rowHider );
	$(document).on('click', '.rowMarkHave', rowMarkHave );
	$(document).on('click', '.rowMarkMaybe', rowMarkMaybe );
} );

function rowHider() {
	// console.log('you clicked a hider!');
	var par = $(this).parents("tr");
	// console.log( par );
	$(par).hide();
	$(".rowReset").fadeIn('fast');
}

function rowMarkHave() {
	var par = $(this).parents("tr");
	$(par).toggleClass('markHave');
}

function rowMarkMaybe() {
	var par = $(this).parents("tr");
	$(par).toggleClass('markMaybe');
}
