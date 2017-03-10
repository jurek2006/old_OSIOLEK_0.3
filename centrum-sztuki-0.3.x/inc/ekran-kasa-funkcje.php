<?php 
// =======================================================================================================
// --------------------- STRONA FUNKCJI PRZYDATNYCH W EKRAN-KASA (używane w ekran.php i admin-ekran-page.php)
// włączana za pomocą require_once
// =======================================================================================================

function czyGenerowacEkranKasa(){
// funkcja sprawdzająca czy dla bieżącego dnia (lub dnia "udającego bieżący") należy wygenerować zawartość ekran_kasa 
// zwraca datę dnia (w formacie "Y-m-d") dla którego należy wykonać generowanie (tylko jeśli należy je wykonać)
// zwraca false jeśli nie należy generować
// zwraca NULL jeśli wystąpił błąd 

	// $data_generowania to data dzisiejsza (Może być jednak modyfikowana poniżej, w celach testowych za pomocą pods ekran_kasa_ustawienia)
	$data_generowania = date("Y-m-d");

	// Pobieranie danych z pods ustawień ekran_kasa_ustawienia
	$pods_ekran_kasa_ustawienia = pods( POD_EKRAN_KASA_USTAWIENIA, array( 'limit' => 1));
	if(!empty($pods_ekran_kasa_ustawienia)){
		$data_generowania_testowe_pods 	= $pods_ekran_kasa_ustawienia->display('dzisiaj_testowe');
		$dzien_wygenerowany_pods 		= $pods_ekran_kasa_ustawienia->display('dzien_wygenerowany');

		if(!empty($data_generowania_testowe_pods)){
		// Jeśli ustawiono wartość pola pods dzisiaj_testowe to ekran traktuje dalej ustawioną tam datę jako dzisiejszą (datę do generowania)
			$data_generowania = $data_generowania_testowe_pods;
		}	

		
		if($dzien_wygenerowany_pods != $data_generowania){
		// Sprawdzenie, czy należy wygenerować nową zawartość ekranu
		// Jeśli data $data_generowania jest różna od $dzien_wygenerowany_pods to zwraca się $data_generowana jako datę do generacji
			logToFile("Stwierdzenie konieczności generowania dla: " . $data_generowania, 'ekran-kasa-funkcje'); //DIAGNOSTYCZNE
			return $data_generowania;

		}
		else{
		// jeśli dla danej daty już jest wygenerowane - zwraca false
			consoleLog("Dzień ".$data_generowania." już był generowany"); //DIAGNOSTYCZNE
			logToFile("Dzień już wygenerowany: " . $data_generowania, 'ekran-kasa-funkcje'); //DIAGNOSTYCZNE
			return false;
		}

	}//if(!empty($pods_ekran_kasa_ustawienia))
	else{
	// jeśli nie udało się dostać do ustawień w POD_EKRAN_KASA_USTAWIENIA zwraca null (wcześniej wyświetla komunikat)
		echo "<b>Błąd pobierania ustawień ekran_kasa_ustawienia</b>";
		logToFile('Błąd pobierania ustawień ekran_kasa_ustawienia', 'ekran-kasa-funkcje'); //DIAGNOSTYCZNE
		return NULL;
	}

}//function czyGenerowacEkranKasa()

function generujEkranKasa(){
// Funkcja generująca wpisy repertuaru na ekran w kasie (czyli do pods ekran_kasa) na zadany dzień 
// Standardowo jest to dzień dzisiejszy (można to testowo "oszukać" ustawiając w pod POD_EKRAN_KASA_USTAWIENIA pole dzisiaj_testowe)	
// Pomijane są wydarzenia, które mają wybrane TRUE dla pola 'wylacz_z_ekran_kasa'

	$dzien = czyGenerowacEkranKasa(); //sprawdza czy generować dla danego dnia, jeśli tak, zwraca datę dnia do generacji

	if(!$dzien){
	// jeśli $dzien jest false lub NULL tzn. że nie trzeba lub nie można (co na jedno wychodzi) generować zawartości ekran-kasa
		return false;
	}
	else{
	// wykonanie faktycznego generowania

		logToFile("Rozpoczęcie generowania repertuaru dnia dla daty: " . $dzien, 'ekran-kasa-funkcje'); //DIAGNOSTYCZNE

		$datetime = new DateTime($dzien);
		$dzien_szukany = $datetime->format('Y-m-d');

		// Na początku funkcja pobiera projekcje danego dnia i wydarzenia z sali widowiskowej danego dnia i dodaje je do tablicy ekran_kasa
		// Można by pominąć zapisywanie do tablicy i od razu dodawać do pods ekran_kasa, ale jeśli później będę chciał dodać np. sortowanie to taka tablica może się przydać

		// POBIERANIE PROJEKCJI FILMOWYCH DANEGO DNIA

		$brak_projekcji = true; //funkcja przechowująca informację o tym, czy nie ma znalezionych projekcji filmowych danego dnia
		$czy_jest_projekcja2d = false;
		$czy_jest_projekcja3d = false; //dwie zmienne stwierdzające, czy danego dnia jest jakaś projekcja 2d i 3d (w zależności od tego wyświetlane są określone ceny w dopisku)

							
		$params = array( 	'limit' => -1,
							'where' => 'DATE( termin_projekcji.meta_value ) = "'.$dzien_szukany.'"',
							'orderby'  => 'termin_projekcji.meta_value');
		

		$pods = pods( 'projekcje', $params );
		//loop through records
		if ( $pods->total() > 0 ) {
		// jeżeli znaleziono jakieś projekcje danego dnia

			$brak_projekcji = false; //znaleziono projekcje kinowe danego dnia, więc zmienna ustawiana na fałsz

			while ( $pods->fetch() ) {

				$tytul_filmu =  $pods->display('film');
				$termin_projekcji = $pods->field('termin_projekcji' );
				$q2d3d = $pods->field('2d3d');
				$projekcja_wersja_jezykowa = $pods->display('wersja_jezykowa');
				$standardowa_wersja_jezykowa = $pods->display('film.standardowa_wersja_jezykowa');


				// Dodawanie "dodatków" związanych z wersją (3d, język) do tytułu filmu
				if($q2d3d){ 
					$tytul_filmu.=' 3D'; //jeśli projekcja jest 3d (czyli TRUE) dodaje taki dopisek do tytułu
					$czy_jest_projekcja3d = true; //ustawia na true zmienną informującą o istnieniu projekcji 3d danego dnia
				} 
				else{
				// jeśli jest to projekcja 2d
					$czy_jest_projekcja2d = true; //ustawia na true zmienną informującą o istnieniu projekcji 3d danego dnia
				}
	            if(!empty($projekcja_wersja_jezykowa)){ 
	            //jeśli wybrano wersję językową dla projekcji to jest ona zapisywana w polu komentarz
	                $komentarz = "/$projekcja_wersja_jezykowa"; 
	            }
	            else if(!empty($standardowa_wersja_jezykowa)){
	            //jeśli wybrano wersję językową dla filmu (i nie jest ona nadpisana przez wersję projekcji
	            //to jest ona wyświetlana w polu komentarza
	                $komentarz = "/$standardowa_wersja_jezykowa"; 
	            }
	            else{
	            // jeśli także nie zdefiniowano wersji językowej dla filmu to komentarz zostaje pusty
	            	$komentarz = '';
	            }

				// Wypełnienie tablicy ekran_kasa projekcjami pobranymi dla danego dnia
				$ekran_kasa[] = array(	'title' => pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji).' - '.$tytul_filmu,
										'godzina' => pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji), 
										'nazwa_wydarzenia' => $tytul_filmu,
										'komentarz' => $komentarz);

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
		
		$pods = pods( POD_EKRAN_KASA, $params );
		//loop through records
		if ( $pods->total() > 0 ) {
			logToFile("Usunięcie wszystkich wygenerowanych wcześniej wpisów w pods ekran_kasa",POD_EKRAN_KASA);

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
			    $new_id = pods(POD_EKRAN_KASA)->add($element_ekran_kasa);
			    logToFile(sprintf("Dodanie do ekran_kasa id %s %s", $new_id, $element_ekran_kasa['title'], $new_id),POD_EKRAN_KASA);
			}
		}

		// GENEROWANIE DOPISKU POD TABELĄ REPERTUARU - zawierającego ceny na podstawie projekcji filmowych danego dnia (wydarzenia nie są uwzględniane)

		$dopisek_do_zapisania = ""; //standardowo wartość do zapisania jako dopisek jest pusta, jeśli nie znaleziona zostanie żadna projekcja, to tak wartość to zostanie zapisana
		// i tak wyświetlana (a właściwie nie, bo to pusty string) będzie na ekran
		$dopisek_do_zapisania2 = ""; //j.w. - druga linia dopisku

		if(!$brak_projekcji){
		// jeśli znaleziono jakieś projekcje danego dnia - wypełniany jest dopisek pod tabelą filmów


			// sprawdzenie, czy dzień tygodnia nie jest uwzględniony w pods cennik_dni_kalendarz (jest to pods wyjątków cen - np. w jakąś środę jest cennik weekendowy)
			$params = array( 	'limit' => 1,
								'where'   => 'DATE(data.meta_value) = "'.$dzien.'"');

			$pods = pods( 'cennik_dni_kalendarz', $params );

			function cennik_pods_do_tablicy($pods){
				
				// tablica cen dla danego dnia - funkcja wczytuje z pods ceny dla rodzajów biletów zdefiniowanych w $tablicaRodzajowCen i przypisuje je do $tablica cen
				// która wygląda w efekcie np. $tablicaCen["normalny2d"] == 10, $tablicaCen["normalny3d"] == 15
				// ta tablica jest zwracana jako funkcja wynikowa
				// gdy dla danego rodzaju ceny jest ona w pods zdefiniowana jako ujemna (taki rodzaj biletu nie istnieje), to rodzaj ten nie jest zwracany w tablicy wynikowej

				function zamien_zaokraglij_cene($cena){
				// funkcja pobierająca cenę (która jest wyciągana z pods) - w typie string, standardowo np. 10.00
				// funkcja sprawdza, czy można zaokrąglić kwotę (rozdzielać zł od gr mogą . lub ,)
				// jeśli tak - to zwraca zaokrągląną, np 10
				// jeśli nie, to zwraca to samo co otrzymała w zmiennej $cena

					if(!is_string($cena)){
					// sprawdzenie czy cena jest podana jako string (tak powinno być)
					// jeśli nie jest, cała funkcja zwraca Błąd
						return 'Błąd';	
					}
					// Znalezienie kropki lub przecinka oddzielających część złotych od groszy
					// $pos przyjmuje pozycję kropki/przecinka w stringu
					// jeśli nie znaleziono . to zwraca False i szukany jest przecinek
					$pos = strpos($cena, '.');
					if($pos == false){
						$pos = strpos($cena, ',');
					}
					// jeśli $pos nie jest false tzn. że znaleziono kropkę lub przecinek
					if($pos != false)
					{
						// na podstawie znalezionej kropki/przecinka kwota rozdzielana jest na część zł i gr
						$zl = substr($cena, 0, $pos);
						$gr = substr($cena, $pos+1, 2);
						if($gr == '00'){
						//jeśli część groszowa jest równa '00' tzn., że można zaokrąglić kwotę do zł i zwrócić jako wynik funkcji
							return $zl;
						}
					}
					// jeśli kwota nie została zaokrąglona, zwracana jest dokładnie taka sama "wartość" (bo to powinien być strong)
					return $cena;
				}

				$tablicaCen = array(); 
				// tablica rodzajów biletów
				$tablicaRodzajowCen = array("normalny2d", "ulgowy2d", "rodzinny2d", "grupowy2d", "normalny3d", "ulgowy3d", "rodzinny3d", "grupowy3d");
				foreach ($tablicaRodzajowCen as $rodzajCeny){
					$cena = $pods->display('cennik.'.$rodzajCeny);
					if($cena >= 0){
						$tablicaCen[$rodzajCeny] = zamien_zaokraglij_cene($cena);
					}
				}

				return $tablicaCen;
			}//function cennik_pods_do_tablicy($pods)

			if ( $pods->total() > 0 ) {
			// jeśli w pods cennik_dni_kalendarz znaleziono wpis z cennikiem niestandardowym dla danego dnia - zostaje użyty cennik zdefiniowany w pods cennik_dni_tygodnia
	            while ( $pods->fetch() ) {
					$nazwa_cennika = $pods->display('name'); //Pobranie nazwy używanego cennika w celach diagnostycznych - wyświetlenia tej 
					consoleLog("Użyty cennik niestandardowy ".$nazwa_cennika); 
					$cennik_dla_dnia = cennik_pods_do_tablicy($pods);

				}//while ( $pods->fetch() )

			}//if ( $pods->total() > 0 )
			else{
			// jeśli w pods cennik_dni_kalendarz nie znaleziono wpisu z cennikiem niestandardowym dla danego dnia - zostaje użyty cennik zdefiniowany dla dnia tygodnia w pods cennik_dni_tygodnia
		
				$dzienTygodnia = pobieczCzescDaty('w',$dzien); //pobranie dnia tygodnia dla aktualnie przetwarzanej daty $dzien (gdzie 0 to Niedziela)
				
				$params = array( 	'limit' => 1,
									'where'   => 'dzien_tygodnia.meta_value = '.$dzienTygodnia);

				$pods = pods( 'cennik_dni_tygodnia', $params );
				if(!empty($pods)){

					$title = $pods->display('title'); //Pobranie nazwy używanego cennika w celach diagnostycznych - wyświetlenia tej 
					consoleLog("Użyto cennik standardowy ".$title);

					$cennik_dla_dnia = cennik_pods_do_tablicy($pods);
				}//if(!empty($pods))

			}//else - if ( $pods->total() > 0 )

			// generowanie dopiska
			$dod_2d = ""; //dopisek do nazwy biletów 2d (standardowo to pusty string) - tylko gdy jest jakaś projekcje 3d danego dnia dla rozróżnienia dodawana jest wartość " 2D"

			if($czy_jest_projekcja3d){
			// jeśli jest znaleziona przynajmniej jedna projekcja 3d danego dnia ($czy_jest_projekcja3d == true) do dopisku pobierane są i dodawane informacje o biletach 3d
			// brane są pod uwagę tylko bilety normalne i ulgowe (jeśli są)
			// zapisuje informacje o biletach w $dopisek_do_zapisania (który jest zapisywany w pods w podstawowym polu dopisek, ale jeśli w dalszym kroku znalezione zostaną inf. o biletach 2d dla danego dnia, to 3d zostaną przekazane do $dopisek_do_zapisania2 - zapisywanego w dopisek2)

				$dod_2d = " 2D"; // dopisanie do nazwy biletów 2d dodatku 2D dla rozróżnienia od 3d

				if(array_key_exists('normalny2d', $cennik_dla_dnia)){

					$dopisek_do_zapisania .= "bilet normalny 3D ".$cennik_dla_dnia['normalny3d']." zł, "; //wygenerowanie /dodanie do $dopisek_do_zapisania/ tekstu np. "bilet normalny 3D 11.00 zł, "

				}
				if(array_key_exists('ulgowy2d', $cennik_dla_dnia)){

					$dopisek_do_zapisania .= "bilet ulgowy 3D ".$cennik_dla_dnia['ulgowy3d']." zł, "; //wygenerowanie /dodanie do $dopisek_do_zapisania/ tekstu np. "bilet ulgowy 3D 11.00 zł, "

				}

			}//if($czy_jest_projekcja3d)
			if($czy_jest_projekcja2d){
			// jeśli jest znaleziona przynajmniej jedna projekcja 2d danego dnia ($czy_jest_projekcja2d == true) do dopisku pobierane są i dodawane informacje o biletach 2d
			// jeśli wystąpiły także bilety 3d tego dnia, to 2d będą wyróżnione w sposób: bilet normalny 2D, bilet ulgowy 2D (standardowo tylko jako bilet normalny, bilet ulgowy)
				$dopisek2d = ""; //dopisek tymczasowy
				
				if(array_key_exists('normalny2d', $cennik_dla_dnia)){

					$dopisek2d .= "bilet normalny".$dod_2d." ".$cennik_dla_dnia['normalny2d']." zł, "; //wygenerowanie /dodanie/ tekstu np. "bilet normalny 2D 11.00" lub "bilet normalny 11.00 zł, "

				}
				if(array_key_exists('ulgowy2d', $cennik_dla_dnia)){

					$dopisek2d .= "bilet ulgowy".$dod_2d." ".$cennik_dla_dnia['ulgowy2d']." zł, "; //wygenerowanie /dodanie/ tekstu np. "bilet ulgowy 2D 11.00" lub "bilet ulgowy 11.00 zł, "

				}

				// Dodanie do $dopisek_do_zapisania dopisku dla biletów 2d
				// (informacja o biletach 3d, jeśli jest, znajdująca się w $dopisek_do_zapisania zostaje najpierw przeniesiona do $dopisek_do_zapisania2)
				$dopisek_do_zapisania2 = $dopisek_do_zapisania;
				$dopisek_do_zapisania = $dopisek2d;

			}// if($czy_jest_projekcja2d)

		}//if(!$brak_projekcji)

		// sprawdzenie, czy ostatni w treści w linii $dopisek_do_zapisania i $dopisek_do_zapisania2 jest przecinek, jeśli tak, to usunięcie go
		function obetnij_ostatni_przecinek($string){
		// funkcja sprawdzająca, czy ciąg $string kończy się przecinkiem, jeśli tak, to jest on usuwany i zwracany bez tego przecinka
		// jeśli nie kończy się przecinkiem, to $string jest zwracany bez zmian (nie licząc przycięcia białych spacji)

			// wycięcie białych spacji na początku i na końcu
			$string = trim($string);

			if(strlen($string) > 0 && $string[strlen($string) - 1] == ','){

				return substr($string, 0, -1);
			}
			else{

				return $string;
			}
		}

		$dopisek_do_zapisania = obetnij_ostatni_przecinek($dopisek_do_zapisania);
		$dopisek_do_zapisania2 = obetnij_ostatni_przecinek($dopisek_do_zapisania2);

		// Zapisanie treści dopiska i dopiska2 do pods, z którego będzie wyświetlał ekran
		// Jeśli nie znaleziono wyżej projekcji dopisek (i dopisek2) ten będzie zapisany jako pusty
		// zapisanie też wyswietlaj_komentarze jako 'tak' (domyślnie zawsze dodawane są komentarze do projekcji)
		// !!!!zapisanie $dzien jako wartości pola dzien_wygenerowany

		$params = array( 'limit' => -1);
		$pods = pods( POD_EKRAN_KASA_USTAWIENIA, $params );
		if(!empty($pods)){
			$pods->save( 'dopisek', $dopisek_do_zapisania);
			$pods->save( 'dopisek2', $dopisek_do_zapisania2);
			$pods->save( 'wyswietlaj_komentarze', 'tak');
			$pods->save( 'dzien_wygenerowany', $dzien ); 
		}//if(!empty($pods))

	}//else - if(!$dzien)

}//generujEkranKasa

?>