$(document).ready(function(){
	initNavigatorDate();
});
/*** JQuery Based timer ***/
var dayNames = Array(
	'dimanche',
	'lundi',
	'mardi',
	'mercredi',
	'jeudi',
	'vendredi',
	'samedi'
);

var monthNames = Array(
	'janvier',
	'février',
	'mars',
	'avril',
	'mai',
	'juin',
	'juillet',
	'août',
	'septembre',
	'octobre',
	'novembre',
	'décembre'
);

function formatSeconds( value ){
	if( value < 10 ){
		value = '0' + value ;
	}
	return value;
}

function initNavigatorDate(){
	$('#dateContent').html('Nous sommes le <span id="date"></span>&nbsp;- il est actuellement &nbsp;	<span id="time"></span>');
	updateNavigatorDate();
	setInterval( 'updateNavigatorDate()', 1000 );
}

function updateNavigatorDate(){
	myDate = new Date;

	$('#date').text(
		dayNames[myDate.getDay()] + ' '
		+ myDate.getDate() + ' '
		+ monthNames[myDate.getMonth()]
	);

	$('#time').text(
		myDate.getHours() + ' : '
		+ myDate.getMinutes() + ' min - '
		+ formatSeconds( myDate.getSeconds() ) + ' sec'
	);
}