// =======================================================================================================
// --------------------- SKRYPTY DOŁĄCZANE DO ekran.php
// włączana za pomocą mechanizmu WP w functions
// w tym funkcja obsługi zegarka
// =======================================================================================================

// KONFIGURACYJNE:
var zadanyCzasTestowy = false; //jeśli przypisane 0 lub false to żadnych zmian - godzina taka, jak w systemie 

// -------------------------------------------------------------------------------------------------------
var $ = jQuery.noConflict();

jQuery(document).ready(function(){
	zegarek(zadanyCzasTestowy);
	ukryjMinione(zadanyCzasTestowy);

	
});

function ukryjMinione(zadanyCzasTestowy){

	var czasyProjekcji = [];

	$('table').find('tr').each(function(){
		var czas = ($(this).attr('class')).split('-'); //pobranie hh:mm z klasy projekcji/wydarzenia (a właściwie tr) i rozdzielenie na hh i mm
		var data = new Date();
		data.setHours(czas[0], czas[1]);
		czasyProjekcji[$(this).attr('class')] = data;
	});

	var roznica = roznicaWczasie(zadanyCzasTestowy);

	setInterval(function(){
		// create a newDate() object and extract the second of the current time on the visitor's
		var czasAktualny = new Date(Date.now() + roznica);

		for (var item in czasyProjekcji) {

		    if(czasyProjekcji[item] < czasAktualny){
		    	$('table .'+item).slideUp('slow');
		    }
		}	


	}, 30*1000);
}


function zegarek(zadanyCzasTestowy){
// funkcja "dająca życie" zegarkowi

	var roznica = roznicaWczasie(zadanyCzasTestowy);

	setInterval(function(){
		// create a newDate() object and extract the second of the current time on the visitor's
		var czasAktualny = new Date(Date.now() + roznica);

		var seconds = czasAktualny.getSeconds();
		// extract the minutes
		var minutes = czasAktualny.getMinutes();
		// extract hours
		var hours = czasAktualny.getHours();

		$(".naglowekDnia .min").html((minutes < 10 ? "0" : "") + minutes);
		$(".naglowekDnia .sec").html((seconds < 10 ? "0" : "") + seconds);
		$(".naglowekDnia .godz").html((hours < 10 ? "0" : "") + hours);

		// animowanie fadeIn fadeOut przezroczystości znaków ':' w zegarku (nie użyto .fadeIn, .fadeToggle itd. ze względu na "znikanie" w nich znaków) 
		if($(".srednik").css('opacity') < 1){ 	$(".srednik").fadeTo('slow',1); }
		else{									$(".srednik").fadeTo('slow',0.1);}
		
	}, 1000);
}

function roznicaWczasie(godzina){
// funkcja obliczająca różnicę w czasie dla zadanej godziny, a czasu faktycznego z systemu (zwraca różnicę w milisekundach)
// używana do testowego wyświetlania innej godziny niż rzeczywista (używane i w zegarek() i w ukryjMinione)
	if(godzina == false){
		return 0;
	}
	else{
		var zadanaGodzina = godzina.split(':');
		var zadanyCzas = new Date();
		zadanyCzas.setHours(zadanaGodzina[0], zadanaGodzina[1]);
		var czasAktualny = new Date();
		return zadanyCzas - czasAktualny;
	}
}

