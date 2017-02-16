<?php
/*
Template Name: Ekran
Description: Obsługuje podstronę do wyświetlania tabeli z repertuarem na ekranie(telewizorze) w kasie biletowej

*/

get_header("ekran"); ?>

<?php
// ukrycie paska admina
show_admin_bar( false );

// $dzisiaj to data dzisiejsza (Może być jednak modyfikowana poniżej, w celach testowych za pomocą pods ekran_kasa_ustawienia)
// na jej podstawie generowany i wyświetlany jest repertuar na ekran
$dzisiaj = date("Y-m-d");

// Pobieranie danych z pods ustawień ekran_kasa_ustawienia
$params = array( 'limit' => -1);
$pods = pods( 'ekran_kasa_ustawienia', $params );
if(!empty($pods)){
	$dzisiaj_testowe = $pods->display('dzisiaj_testowe');
	$dzien_wygenerowany = $pods->display('dzien_wygenerowany');

	if(!empty($dzisiaj_testowe)){
	// Jeśli ustawiono wartość pola pods dzisiaj_testowe to ekran traktuje dalej ustawioną tam datę jako dzisiejszą
		$dzisiaj = $dzisiaj_testowe;
	}	

	// Sprawdzenie, czy należy wygenerować nową zawartość ekranu
	// Jeśli data $dzisiaj jest różna od $dzien_wygenerowany to generuje się nową zawartość i ustawia w pods jako dzien_wygenerowany datę $dzisiaj
	if($dzien_wygenerowany != $dzisiaj){
		consoleLog("generowanie dla daty ".$dzisiaj);

		// wygenerowanie nowej zawartości ekranu
		generujDzien($dzisiaj);

		// zapisanie $dzisiaj jako wartości pola dzien_wygenerowany 
		$pods->save( 'dzien_wygenerowany', $dzisiaj ); 

	}
	else{
		consoleLog("Dzień ".$dzisiaj." już był generowany");
	}

}//if(!empty($pods))
else{
	echo "<b>Błąd pobierania ustawień ekran_kasa_ustawienia</b>";
}

// WYŚWIETLANIE EKRANU --------------------------------------------------------------------------------------------------------------------------------

wyswietlajRepertuaryConsole('2017-01-15',30); //TESTOWE
wyswietlajRepertuarDnia();
// ----------------------------------------------------------------------------------------------------------------------------------------------------

function generujDzien($dzien = NULL){
// Funkcja generująca wpisy repertuaru na ekran w kasie (czyli do pods ekran_kasa) na zadany dzień 
// Standardowo jest to dzień dzisiejszy (w przypadku gdy nie podano $dzien tak jest generowane)	
// Pomijane są wydarzenia, które mają wybrane TRUE dla pola 'wylacz_z_ekran_kasa'

	if(is_null($dzien) || !walidujDate($dzien)){
	//Jeśli parametr $dzien jest NULL lub nie zawiera prawidłowej daty w formacie "Y-m-d" czyli YYYY-MM-DD
	//to przypisywana jest mu data dzisiejsza
	//korzysta z funkcji walidujDatę z functions.php
		$dzien = date("Y-m-d");
	}

	$datetime = new DateTime($dzien);
	$dzien_szukany = $datetime->format('Y-m-d');

	// Na początku funkcja pobiera projekcje danego dnia i wydarzenia z sali widowiskowej danego dnia i dodaje je do tablicy ekran_kasa
	// Można by pominąć zapisywanie do tablicy i od razu dodawać do pods ekran_kasa, ale jeśli później będę chciał dodać np. sortowanie to taka tablica może się przydać

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
			$standardowa_wersja_jezykowa = $pods->display('film.standardowa_wersja_jezykowa');


			// Dodawanie "dodatków" związanych z wersją (3d, język) do tytułu filmu
			if($q2d3d){ $tytul_filmu.=' 3D';	} //jeśli projekcja jest 3d (czyli TRUE) dodaje taki dopisek do tytułu
            if(!empty($projekcja_wersja_jezykowa)){ 
            //jeśli wybrano wersję językową dla projekcji to jest ona wyświetlana w tytule
                $tytul_filmu.= " /$projekcja_wersja_jezykowa"; 
            }
            else if(!empty($standardowa_wersja_jezykowa)){
                //jeśli wybrano wersję językową dla filmu (i nie jest ona nadpisana przez wersję projekcji
                //to jest ona wyświetlana w tytule
                $tytul_filmu.= " /$standardowa_wersja_jezykowa"; 
            }

			// Wypełnienie tablicy ekran_kasa projekcjami pobranymi dla danego dnia
			$ekran_kasa[] = array(	'title' => pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji).' - '.$tytul_filmu,
									'godzina' => pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji), 
									'nazwa_wydarzenia' => $tytul_filmu);

	    }//while ( $pods->fetch() )

	}//if ( $pods->total() > 0 )


	// POBIERANIE WYDARZEŃ W SALI WIDOWISKOWEJ OWE DANEGO DNIA

	// Pobieranie tylko wydarzeń danego dnia odbywających się w lokalizacji o slug'u "sala-widowiskowa-owe-odra"
	// I tylko wydarzeń, które nie mają zaznaczonego (czylu TRUE pola 'wylacz_z_ekran_kasa')

	$params = array( 	'limit' => -1,
		'where'   => 'DATE(data_i_godzina_wydarzenia.meta_value) = "'.$dzien_szukany.'" AND lokalizacje.slug LIKE "%sala-widowiskowa-owe-odra%" AND wylacz_z_ekran_kasa.meta_value = FALSE',
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

			// Wypełnienie tablicy ekran_kasa wydarzeniami pobranymi dla danego dnia
			testoweConsoleLog('Pobieranie wydarzenia '.pobieczCzescDaty('G',$data_i_godzina_wydarzenia).':'.pobieczCzescDaty('i',$data_i_godzina_wydarzenia).' - '.$title);
			$ekran_kasa[] = array(	'title' => pobieczCzescDaty('G',$data_i_godzina_wydarzenia).':'.pobieczCzescDaty('i',$data_i_godzina_wydarzenia).' - '.$title,
									'godzina' => pobieczCzescDaty('G',$data_i_godzina_wydarzenia).':'.pobieczCzescDaty('i',$data_i_godzina_wydarzenia), 
									'nazwa_wydarzenia' => $title
									);
		}//while ( $pods->fetch() )
	}//if ( $pods->total() > 0 )

	// USUNIĘCIE wszystkich wygenerowanych wcześniej wpisów w pods ekran_kasa
	$params = array( 'limit' => -1);
	
	$pods = pods( 'ekran_kasa', $params );
	//loop through records
	if ( $pods->total() > 0 ) {

		while ( $pods->fetch() ) {
			$pods->delete(); 
		}
	}

	// DODAWANIE ELEMENTÓW Z TABLICY $ekran_kasa do pods ekran_kasa - to będę projekcje/wydarzenia do wyświetlenia na ekran

	if(isset($ekran_kasa) && count($ekran_kasa)>0){

		// Przesortowanie tablicy $ekran_kasa względem godziny
		usort($ekran_kasa, function($a, $b) {
		    return $a['godzina'] <=> $b['godzina'];
		});

		// jeśli jest cokolwiek znalezione na szukany dzień, to każda ze znalezionych projekcji/wydarzeń jest dodawana do pods ekran_kasa
		// Do każdego elementu dodawany jest licznik używany później przy sortowaniu wyświetlania
		$licznik = 1;
		foreach ($ekran_kasa as $element_ekran_kasa) {
			$element_ekran_kasa['kolejnosc'] = $licznik++;
		    $new_id = pods('ekran_kasa')->add($element_ekran_kasa);
		}
	}


}//generujDzien

function wyswietlajRepertuarDnia(){
// Funkcja wyświetlająca repertuar na ekran w kasie - na podstawie wygenerowanej zawartości danego dnia 
// Sortowanie wyswtetlania wydarzeń odbywa się na podstawie wartości pola kolejnosc

	// Pobieranie z pods ustawień ekran_kasa_ustawienia dnia dla którego jest wygenerowana zawartośc ekranu 
	// oraz pobieranie zdefiniowanych w tym samym pods wielkości czcione
	$params = array( 'limit' => -1);
	$pods = pods( 'ekran_kasa_ustawienia', $params );
	if(!empty($pods)){
		$dzien_wygenerowany = $pods->display('dzien_wygenerowany');
		$filmy_font_size = $pods->display('filmy_font_size');
		$dopisek_font_size = $pods->display('dopisek_font_size');

		if(empty($dzien_wygenerowany)){
		// Jeśli nie ma wartości dnia wygenerowanego - nastąpił jakiś błąd
			echo "<b>Błąd pobierania wartośco ustawień dnia wygenerowanego z ekran_kasa_ustawienia</b>";
		}

		if(!empty($filmy_font_size)){
		// Jeśli w pods ekran_kasa_ustawienia zdefiniowano wielkość czcionki (a jest to pole *require) to dodawany jest odpowiedni styl
		// Wielkość czcionki jest w procentach
			echo '	<style type="text/css">
						table{font-size: '.$filmy_font_size.'%;}
						p.dopisek{font-size: '.$dopisek_font_size.'%;}
					</style>'; 

		}


	}//if(!empty($pods))
	else{
		echo "<b>Błąd pobierania ustawień ekran_kasa_ustawienia</b>";
	}

	?>
		<p class="naglowekDnia"><?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_wygenerowany)).', '.zamienDateNaTekst($dzien_wygenerowany, $bez_roku=FALSE);  ?></p>
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
	
	$pods = pods( 'ekran_kasa', $params );

	if ( $pods->total() > 0 ) {


		while ( $pods->fetch() ) {

			$nazwa_wydarzenia =  $pods->display('nazwa_wydarzenia');
			$godzina = $pods->display('godzina' );

			?>
			<tr><td><?php echo $godzina.'&nbsp'?></td><td><?php echo strip_tags($nazwa_wydarzenia)?></td></tr>
			<?php

	    }//while ( $pods->fetch() )

	    

	}//if ( $pods->total() > 0 )
	else{
		?>
		<tr><td colspan="2"><h1>Brak wydarzeń</td></tr>
		<?php
	}

	?>
		</table><!-- koniec tabeli repertuaru -->
		
	<?php
		// POBIERANIE I WYŚWIETLANIE DOPISKU POD TABELĄ z pods
		$params = array( 'limit' => -1);
		$pods = pods( 'ekran_kasa_ustawienia', $params );
		if(!empty($pods)){
			$dopisek = $pods->display('dopisek');
				if(!empty($dopisek)){
				// Jeśli jest wpisana jakaś zawartość w dopisek, to jest on wyświetlany pod tabelą repertuaru
					echo '<p class="dopisek">'.strip_tags($dopisek).'</p>';
				}
			
		}//if(!empty($pods))

}//wyswietlajRepertuarDnia

// TESTOWE

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