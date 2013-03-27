$(document).ready( function() {
	$(document).on('click', '.rowHider', rowHider );
	$(document).on('click', '.rowMarker', rowMarker );
} );

function rowHider() {
	// console.log('you clicked a hider!');
	var par = $(this).parents("tr");
	// console.log( par );
	$(par).hide();
	$(".rowReset").fadeIn('fast');
}

function rowMarker() {
	var par = $(this).parents("tr");
	$(par).toggleClass('marked');

}
