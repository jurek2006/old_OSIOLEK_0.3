// =======================================================================================================
// --------------------- SKRYPTY DOŁĄCZANE DO ekran.php
// włączana za pomocą mechanizmu WP w functions
// w tym funkcja obsługi zegarka
// =======================================================================================================
var $ = jQuery.noConflict();


jQuery(document).ready(function(){
	zegarek();
	
});

function zegarek(){
// funkcja "dająca życie" zegarkowi

	// Create a new Date() object
	var newDate = new Date();

	// Extract the current date from Date object
	newDate.setDate(newDate.getDate());

	setInterval(function(){
		// create a newDate() object and extract the second of the current time on the visitor's
		var seconds = new Date().getSeconds();
		// extract the minutes
		var minutes = new Date().getMinutes();
		// extract hours
		var hours = new Date().getHours();

		$(".naglowekDnia .min").html((minutes < 10 ? "0" : "") + minutes);
		$(".naglowekDnia .sec").html((seconds < 10 ? "0" : "") + seconds);
		$(".naglowekDnia .godz").html((hours < 10 ? "0" : "") + hours);
	}, 1000);
}



