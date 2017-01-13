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
						
	$params = array( 	'limit' => -1,
						'where' => 'DATE( termin_projekcji.meta_value ) = "'.$dzien_szukany.'"',
						'orderby'  => 'termin_projekcji.meta_value');
	

	$pods = pods( 'projekcje', $params );
	//loop through records
	if ( $pods->total() > 0 ) {

		$tytul_filmu =  $pods->display('film');
		$termin_projekcji = $pods->field('termin_projekcji' );
		$q2d3d = $pods->field('2d3d');
		$projekcja_wersja_jezykowa = $pods->display('wersja_jezykowa');

		while ( $pods->fetch() ) {
			?>
	                <?php echo pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji).' - '.$tytul_filmu ?>
		    </p>
		    <?php
	    }//while ( $pods->fetch() )

	}//if ( $pods->total() > 0 )

	echo 'Repertuar na dzień '.$dzien;
}//wyswietlajRepertuarDnia

wyswietlajRepertuarDnia();
?>

<?php get_footer("ekran"); ?>