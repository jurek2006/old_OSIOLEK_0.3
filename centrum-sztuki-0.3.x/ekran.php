<?php
/*
Template Name: Ekran
Description: Obsługuje podstronę do wyświetlania tabeli z repertuarem na ekranie(telewizorze) w kasie biletowej

*/

get_header("ekran"); 

// wczytanie funkcji przydatnych przy obsłudze (i ustawieniach) ekranu w kasie z pliku
require get_template_directory(). '/inc/ekran-kasa-funkcje.php';
// ---------------------------------------------------------------------------------------------------------------------------


// ukrycie paska admina
show_admin_bar( false );


// WYGENEROWANIE WPISÓW DNIA, JEŚLI JEST TO POTRZEBNE
generujEkranKasa();

// WYŚWIETLANIE EKRANU --------------------------------------------------------------------------------------------------------------------------------

wyswietlajRepertuarDnia();
// ----------------------------------------------------------------------------------------------------------------------------------------------------


function wyswietlajRepertuarDnia(){
// Funkcja wyświetlająca repertuar na ekran w kasie - na podstawie wygenerowanej zawartości danego dnia 
// Sortowanie wyswtetlania wydarzeń odbywa się na podstawie wartości pola kolejnosc

	// Pobieranie z pods ustawień ekran_kasa_ustawienia dnia dla którego jest wygenerowana zawartośc ekranu 
	// oraz pobieranie zdefiniowanych w tym samym pods wielkości czcione
	$params = array( 'limit' => -1);
	$pods = pods( POD_EKRAN_KASA_USTAWIENIA, $params );
	if(!empty($pods)){
		$dzien_wygenerowany = $pods->display('dzien_wygenerowany');
		$filmy_font_size = $pods->display('filmy_font_size');
		$dopisek_font_size = $pods->display('dopisek_font_size');
		$komentarze_font_size = $pods->display('komentarze_font_size'); 
		$wyswietlaj_komentarze = $pods->field('wyswietlaj_komentarze');
		// pobieranie dopisków (dodawanych dalej pod tabelą projekcji)
		$dopisek = $pods->display('dopisek');
		$dopisek2 = $pods->display('dopisek2');


		if(empty($dzien_wygenerowany)){
		// Jeśli nie ma wartości dnia wygenerowanego - nastąpił jakiś błąd
			echo "<b>Błąd pobierania wartośco ustawień dnia wygenerowanego z ekran_kasa_ustawienia</b>";
		}

		if(!empty($filmy_font_size)){
		// Jeśli w pods ekran_kasa_ustawienia zdefiniowano wielkość czcionki (a jest to pole *require) to dodawany jest odpowiedni styl
		// Wielkość czcionki jest w procentach
		// wielkości czcionek w dopisku i w komentarzach jest w procentach względem czcionki głównej
		// więc jeśli będą ustawione na 100[%] to będą takiej samej wielkości jak czcionka projekcji
			echo '	<style type="text/css">
						table{font-size: '.$filmy_font_size.'%;}
						p.dopisek{font-size: '.($filmy_font_size * $dopisek_font_size / 100).'%;}
						span.komentarz{font-size: '.$komentarze_font_size.'%;} 
					</style>'; 

		}


	}//if(!empty($pods))
	else{
		echo "<b>Błąd pobierania ustawień ekran_kasa_ustawienia</b>";
	}

	?>
		<p class="naglowekDnia"><?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_wygenerowany)).', '.zamienDateNaTekst($dzien_wygenerowany, $bez_roku=FALSE);  ?>
			<!-- trochę odstępu -->	&nbsp; 
			<span class="godz">00</span><span class="srednik">:</span><span class="min">00</span>
			<!-- <span class="srednik">:</span><span class="sec">00</span> część odpowiadająca za wyświetlanie sekund, wystarczy ją odkomentować, żeby działała -->
		</p>

		<table><!-- początek tabeli repertuaru -->
		<colgroup>
		    <col class="godziny" />
		    <col/>
		</colgroup>
		<!-- <tr><td colspan="2"></td></tr> --> 
	<?php

	// POBIERANIE WPISÓW WYGENEROWANYCH NA EKRAN (z pods ekran_kasa)
	// Sortowanie odbywa się na podstawie wartośc pola kolejnosc!!!
							
	$params = array( 	'limit' => -1,
						'orderby'  => 'kolejnosc.meta_value');
	
	$pods = pods( POD_EKRAN_KASA, $params );

	if ( $pods->total() > 0 ) {


		while ( $pods->fetch() ) {

			$nazwa_wydarzenia =  $pods->display('nazwa_wydarzenia');
			$godzina = $pods->display('godzina' );
			$komentarz = $pods->display('komentarz');
			?>
			<tr class="<?php echo str_replace(":","-", strip_tags($godzina)) /* dodanie do tr klasy z godziną */?>"><td> 
			<?php echo strip_tags($godzina).'&nbsp'?></td><td><?php echo strip_tags($nazwa_wydarzenia);
			if($wyswietlaj_komentarze){
			// jeśli zaznaczono checkbox wyswietlaj_komentarze wyświetla komentarz przy każdej projekcji
				echo '<span class="komentarz">'.strip_tags($komentarz).'</span>';
			}
			?>
			</td></tr>
			<?php

	    }//while ( $pods->fetch() )

	    

	}//if ( $pods->total() > 0 )
	else{
		?>
		<tr><td colspan="3"><h1>Brak wydarzeń</td></tr>
		<?php
	}

	?>
		</table><!-- koniec tabeli repertuaru -->
		
	<?php
		// WYŚWIETLANIE DOPISKU (i dopisek2) POD TABELĄ z pods
			echo '<p class="dopisek">'.strip_tags($dopisek).'</p>';
			echo '<p class="dopisek">'.strip_tags($dopisek2).'</p>';

}//wyswietlajRepertuarDnia

// TESTOWE:

function wyswietlajRepertuarDniaConsole($dzien = NULL){
	//Funkcja wyświetlająca testowo repertuar kina danego dnia - WERSJA PRÓBNA - pobierająca "na żywo" z projekcji i wydarzeń
	// DZIAŁA TYLKO JEŚLI W FUNCTIONS JEST WŁĄCZONY TRYB TESTOWY
	//Jeśli $dzien zdefiniowany, to wyświetla dla tego dnia, a nie dzisiaj 

	if(TRYB_TESTOWY){

	
		if(is_null($dzien) || !walidujDate($dzien)){
		//Jeśli parametr $dzien jest NULL lub nie zawiera prawidłowej daty w formacie "Y-m-d" czyli YYYY-MM-DD
		//to przypisywana jest mu data dzisiejsza
		//korzysta z funkcji walidujDatę z functions.php
			$dzien = date("Y-m-d");
		}

		$datetime = new DateTime($dzien);
		$dzien_szukany = $datetime->format('Y-m-d');

		testoweConsoleLog('<h2>Repertuar na dzień '.$dzien.'</h2>');

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

				testoweConsoleLog(pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji).' - '.$tytul_filmu);

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

				testoweConsoleLog(pobieczCzescDaty('G',$data_i_godzina_wydarzenia).':'.pobieczCzescDaty('i',$data_i_godzina_wydarzenia).' - '.$title);
			}//while ( $pods->fetch() )
		}//if ( $pods->total() > 0 )
	}//if(TRYB_TESTOWY)
	
}//wyswietlajRepertuarDniaConsole



//TESTOWE

function wyswietlajRepertuaryConsole($dzien_poczatku = NULL, $iloscDni = 0){
// DZIAŁA TYLKO JEŚLI W FUNCTIONS JEST WŁĄCZONY TRYB TESTOWY
//funkcja testowo wyświetlająca repertuar na zadaną ilośc dni
//tak żeby można było sprawdzać działanie funkcji wyswietlajRepertuarDnia
	if(TRYB_TESTOWY){

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
			wyswietlajRepertuarDniaConsole($dzien_szukany);
		}
	}//if(TRYB_TESTOWY)
}//wyswietlajRepertuaryConsole



?>

<?php get_footer("ekran"); ?>