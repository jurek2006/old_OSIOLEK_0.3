<?php 
// =======================================================================================================
// --------------------- STRONA FUNKCJI UŻYWANYCH W KINO (używane w kino.php i filmy_single.php)
// włączana za pomocą require_once
// =======================================================================================================

function generuj_etykiete_dnia($dzien_szukany){
// funkcja generująca etykietę dnia (na potrzeby wyświetlania repertuaru kina)
// wykorzystuje cennik regularny (etykiety z niego) POD_CENNIK_DNI_TYGODNIA i cennik wyjątków 
// zwraca np. Wtorek, 28 lutego 2017, TANIA ŚRODA, 29 lutego 2017, ŚWIĘTO KONSTYTUCJI 3 MAJA, 3 maja 2017 POD_CENNIK_DNI_KALENDARZ

	$etykieta_dnia = false;

	// sprawdzenie, czy dzień tygodnia nie jest uwzględniony w pods cennik_dni_kalendarz (jest to pods wyjątków cen - np. w jakąś środę jest cennik weekendowy)
	$params = array( 	'limit' => 1,
						'where'   => 'DATE(data.meta_value) = "'.$dzien_szukany.'"');

	$pods_kal = pods( POD_CENNIK_DNI_KALENDARZ, $params );


	if ( $pods_kal->total() > 0 ) {
	// jeśli w pods cennik_dni_kalendarz znaleziono wpis z cennikiem niestandardowym dla danego dnia - zostaje użyta etykieta z tego cennika (np. "Święto 3 maja")
        while ( $pods_kal->fetch() ) {
			$nazwa_cennika = $pods_kal->display('name'); //TESTOWE Pobranie nazwy używanego cennika w celach diagnostycznych - wyświetlenia w consoli 
			consoleLog("Użyty cennik niestandardowy ".$nazwa_cennika); //TESTOWE
			$etykieta_dnia = $pods_kal->display('etykieta'); //pobranie etykiety dla wyjątku w kalendarzu
			if(empty($etykieta_dnia)){
			// jeśli etykieta nie jest ustawiona dla tego dnia (wyjątku) to pobierana jest nazwa dnia tygodnia
				$etykieta_dnia = zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_szukany), FALSE);
			}

		}//while ( $pods_kal->fetch() )

	}//if ( $pods_kal->total() > 0 )
	else{
	// jeśli w pods cennik_dni_kalendarz nie znaleziono wpisu z cennikiem niestandardowym dla danego dnia - zostaje użyty cennik (a właściwie etykieta z niego)zdefiniowany dla dnia tygodnia w pods cennik_dni_tygodnia

		$dzienTygodnia = pobieczCzescDaty('w',$dzien_szukany); //pobranie dnia tygodnia dla aktualnie przetwarzanej daty $dzien (gdzie 0 to Niedziela)
		
		$params = array( 	'limit' => 1,
							'where'   => 'dzien_tygodnia.meta_value = '.$dzienTygodnia);

		$pods_kal = pods( POD_CENNIK_DNI_TYGODNIA, $params );
		if(!empty($pods_kal)){

			$title = $pods_kal->display('title'); //TESTOWE Pobranie nazwy używanego cennika w celach diagnostycznych - wyświetlenia jej 
			consoleLog("Użyto cennik standardowy ".$title);

			$etykieta_dnia = $pods_kal->display('etykieta'); //pobranie etykiety dla tego dnia regularnego
			if(empty($etykieta_dnia)){
			// jeśli etykieta nie jest ustawiona dla tego dnia (regularnego) to pobierana jest nazwa dnia tygodnia
				$etykieta_dnia = zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_szukany), FALSE);
			}
		}//if(!empty($pods_kal))

	}//else - if ( $pods_kal->total() > 0 )

	return $etykieta_dnia;

}//generuj_etykiete_dnia($dzien_szukany)


?>