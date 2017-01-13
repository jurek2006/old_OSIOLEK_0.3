<?php
/*
Template Name: Ekran
Description: Obsługuje podstronę do wyświetlania tabeli z repertuarem na ekranie(telewizorze) w kasie biletowej

*/

get_header("ekran"); ?>

<h1>Test ekranu</h1>

<?php

function wyswietlajRepertuarDnia($dzien = NULL){
	//Funkcja wyświetlająca repertuar kina danego dnia - WERSJA NA EKRAN DO KASY
	//Jeśli $dzien zdefiniowany, to wyświetla dla tego dnia, a nie dzisiaj 

	
	if(is_null($dzien) || !walidujDate($dzien)){
	//Jeśli parametr $dzien jest NULL lub nie zawiera prawidłowej daty w formacie "Y-m-d" czyli YYYY-MM-DD
	//to przypisywana jest mu data dzisiejsza
	//korzysta z funkcji walidujDatę z functions.php
		$dzien = date("Y-m-d");
	}

	$datetime = new DateTime($dzien);
	$dzien_szukany = $datetime->format('Y-m-d');

	echo '<h2>Repertuar na dzień '.$dzien.'</h2>';

	// POBIERANIE PROJEKCJI FILMOWYCH DANEGO DNIA
						
	$params = array( 	'limit' => -1,
						'where' => 'DATE( termin_projekcji.meta_value ) = "'.$dzien_szukany.'"',
						'orderby'  => 'termin_projekcji.meta_value');
	

	$pods = pods( 'projekcje', $params );
	//loop through records
	if ( $pods->total() > 0 ) {

		while ( $pods->fetch() ) {

			$tytul_filmu =  $pods->display('film');
			$termin_projekcji = $pods->field('termin_projekcji' );
			$q2d3d = $pods->field('2d3d');
			$projekcja_wersja_jezykowa = $pods->display('wersja_jezykowa');

			echo pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji).' - '.$tytul_filmu.'</br>';

	    }//while ( $pods->fetch() )

	}//if ( $pods->total() > 0 )


	// POBIERANIE WYDARZEŃ W SALI WIDOWISKOWEJ OWE DANEGO DNIA

	// Pobieranie tylko wydarzeń danego dnia odbywających się w lokalizacji o slug'u "sala-widowiskowa-owe-odra"

	$params = array( 	'limit' => -1,
		'where'   => 'DATE(data_i_godzina_wydarzenia.meta_value) = "'.$dzien_szukany.'" AND lokalizacje.slug LIKE "%sala-widowiskowa-owe-odra%"',
		'orderby'  => 'data_i_godzina_wydarzenia.meta_value');

	$pods = pods( 'wydarzenia', $params );
	if ( $pods->total() > 0 ) {
		//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
	    while ( $pods->fetch() ) {
	        //Put field values into variables
	        $title = $pods->display('name');
			$data_i_godzina_wydarzenia = $pods->display('data_i_godzina_wydarzenia');
			$lokalizacje = $pods->field('lokalizacje.slug');
			$lokalizacje = $lokalizacje[0];

			echo pobieczCzescDaty('G',$data_i_godzina_wydarzenia).':'.pobieczCzescDaty('i',$data_i_godzina_wydarzenia).' - '.$title.'</br>';
		}//while ( $pods->fetch() )
	}//if ( $pods->total() > 0 )

	echo "<hr>";
	
}//wyswietlajRepertuarDnia

//TESTOWE

	function wyswietlajRepertuaryTestowo($dzien_poczatku = NULL, $iloscDni = 0){
	//funkcja testowo wyświetlająca repertuar na zadaną ilośc dni
	//tak żeby można było sprawdzać działanie funkcji wyswietlajRepertuarDnia

		if(is_null($dzien_poczatku) || !walidujDate($dzien_poczatku)){
		//Jeśli parametr $dzien_poczatku jest NULL lub nie zawiera prawidłowej daty w formacie "Y-m-d" czyli YYYY-MM-DD
		//to przypisywana jest mu data dzisiejsza
		//korzysta z funkcji walidujDatę z functions.php
			$dzien_poczatku = date("Y-m-d");
		}
		
		for($i=0; $i<=$iloscDni; $i++){
			$datetime = new DateTime($dzien_poczatku);
			$datetime->modify('+'.$i.' day');
			$dzien_szukany = $datetime->format('Y-m-d');
			wyswietlajRepertuarDnia($dzien_szukany);
		}
	}//wyswietlajRepertuaryTestowo

wyswietlajRepertuaryTestowo(NULL,30);

///TESTOWE

?>

<?php get_footer("ekran"); ?>