// =======================================================================================================
// --------------------- SKRYPTY DOŁĄCZANE DO ekran.php
// włączana za pomocą mechanizmu WP w functions
// w tym funkcja obsługi zegarka
// =======================================================================================================

// KONFIGURACYJNE:
var zadanyCzasTestowy = false; //zmienna określająca godzinę wyświetlaną na ekranie (godz. startu) 
// jeśli przypisane 0 lub false to żadnych zmian - godzina taka, jak w systemie 
// (przykładowo zadanyCzasTestowy = "18:00";)

// jeśli zadanyCzasTestowy nie jest poprawnym stringiem opisującym godzinę, to przyjmuje wartość false
if(sprawdzPoprawnoscGodziny(zadanyCzasTestowy) == false){ zadanyCzasTestowy = false; }

// -------------------------------------------------------------------------------------------------------
var $ = jQuery.noConflict();

jQuery(document).ready(function(){
	zegarek(zadanyCzasTestowy); 	//włączenie działania zegarka
	ukryjMinione(zadanyCzasTestowy);//włączenie ukrywania wydarzeń/projekcji minionych
	
});

function ukryjMinione(zadanyCzasTestowy){
// funkcja ukrywająca na ekranie repertuaru wydarzenia, które już się odbyły

	var czasyProjekcji = [];

	$('table').find('tr').each(function(){
	// dla każdego wydarzenia/projekcji dodanego do tabeli (repertuaru na ekranie)
	// dodanie do czasyProjekcji elementu dla którego id to nazwa klasy wydarzenia/projekcji (np. 20-15)
	// a wartość to Date() dla danego dnia i zadanej godziny (np. Date 2017-03-14T19:15:03.955Z)
		var czas = ($(this).attr('class')).split('-'); //pobranie hh:mm z klasy projekcji/wydarzenia (a właściwie tr) i rozdzielenie na hh i mm
		var data = new Date();
		data.setHours(czas[0], czas[1]);
		czasyProjekcji[$(this).attr('class')] = data;
	});

	// obliczenie różnicy (w ms) pomiędzy czasem faktycznym w systemie, a zadanym czasem testowym (później używana do korygowania czasu)
	var roznica = roznicaWczasie(zadanyCzasTestowy);

	setInterval(function(){
		// czasAktualny to czas testowy (czas systemu skorygowany o różnicę)
		var czasAktualny = new Date(Date.now() + roznica);

		for (var item in czasyProjekcji) {
		// sprawdzenie dla każdego wydarzenia/projekcji czy nie minęła już jego godzina

		    if(czasyProjekcji[item] < czasAktualny){
		    	// jeśli minęła już godzina wydarzenia, to jest ono ukrywane (cały tr) w tabeli
		    	$('table .'+item).slideUp('slow');
		    }
		}	


	}, 30*1000); //sprawdzane co 30s
}


function zegarek(zadanyCzasTestowy){
// funkcja "dająca życie" zegarkowi

	// obliczenie różnicy (w ms) pomiędzy czasem faktycznym w systemie, a zadanym czasem testowym (później używana do korygowania czasu)
	var roznica = roznicaWczasie(zadanyCzasTestowy);

	setInterval(function(){
		// czasAktualny to czas testowy (czas systemu skorygowany o różnicę)
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

function sprawdzPoprawnoscGodziny(godzina){
// funkcja sprawdzająca poprawnośc wejściowej zmiennej godzina
// godzina to string zawierający prawidłową godzinę np. '18:11', '9:00'
// rozdzielenie części musi być za pomocą znaku ':'
// nie można stosować myślnika-minusa '-'

	if(typeof godzina != 'string'){return false} //godzina musi być stringiem

	if(godzina.search(/-/) != -1){return false} //godzina nie może zawierać myślnika (minusa) [sprawdzenie na jakim indeksie jest znak '-' jeśli nie ma, to -1]

	var rozdzielone = godzina.split(':');
	if(rozdzielone.length != 2){return false} //godzina po rozdzieleniu na części względem znaku ':' musi mieć dwie części
	if(rozdzielone[1].length != 2 || rozdzielone[0].length < 1 || rozdzielone[0].length > 2){return false} //część minutowa musi mieć długość dwóch znaków a godzinowa 1 lub 2

	var hh = Number(rozdzielone[0]); //częśc godzinowa
	var mm = Number(rozdzielone[1]); //część minutowa

	if( isNaN(hh) ||  isNaN(mm)){ return false} //obie części muszą się konwertować na liczbę
	if(( 0 > hh) || (hh > 23) || ( 0 > mm) || (mm > 59)){ return false} //hh musi się zawierać w zakresie 0-23 a mm w zakresie 0-59 i jeśli tak jest, to funkcja zwraca true
	
	// jeśli przejdzie wszystkie powyższe testy zwraca true
	return true;
}

// TESTOWE =======================================================================================

function testSprawdzPoprawnoscGodziny(){
// funkcja testująca poprawność działania funkcji sprawdzPoprawnoscGodziny(godzina)

	var testowe = [
		[false	, false],
		[true	, false],
		[0		, false],
		['18:15', true],
		['0:00'	, true],
		['9:01'	, true],
		['23:59', true],
		['24:00', false],
		['-9:00', false],
		['18.15', false],
		['0.00'	, false],
		['9.01'	, false],
		['23.59', false],
		['24.00', false],
		['-9.00', false],
		['choom', false],
		[18		, false],
		['-0:00', false],
		['%0:00', false],
		['@0:00', false],
		['18:1'	, false],
		[''		, false],
		['5:5'	, false],
		['55'	, false],
		['teks:pr', false],
		['18:by', false],
		['uy:18', false],
		['18:78', false]
	];

	console.log('testSprawdzPoprawnoscGodziny:');

	var wszystkieTestyOK = true;

	for(var i = 0; i < testowe.length; i++){
		if(sprawdzPoprawnoscGodziny(testowe[i][0]) != testowe[i][1]){
			console.log("Test wykrzaczony: " + testowe[i][0]);
			wszystkieTestyOK = false;
		}
	}

	console.log('Czy wszystkieTestyOK? ' + wszystkieTestyOK);
}

