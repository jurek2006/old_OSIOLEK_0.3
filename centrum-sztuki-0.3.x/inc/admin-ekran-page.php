<h1>Ustawienia ekranu repertuaru w kasie</h1>

<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
<!-- formularz edycji wpisów na stronę ekranu repertuaru w kasie -->
<table><!-- początek tabeli repertuaru -->
		<colgroup>
		    <col class="godziny" />
		    <col/>
		</colgroup>
<?php
	
	if(isset( $_POST["zapisz"])){
	//Jeśli kliknięto przycisk "Zapisz" - czyli przesłano formularz projekcji do zapisania

		class ZmienioneDaneDoPods{
		// klasa przechowujące dane formularza po zatwierdzeniu (zapisaniu) i umożliwiająca zapisanie ich do pods
			private $_nazwa_pods = null;
			private $_godzina = null;
			private $_nazwa_wydarzenia = null;
			private $_id = null;
			private $_count = null;

			public function __construct($nazwa_pods, $godzina, $nazwa_wydarzenia, $id){
				// paramatry:
				// - $nazwa_pods - definiuję nazwę pods do której zapisywane są dane (standardowo tutaj ekran_kasa)
				// - $godzina, $nazwa_wydarzenia, $id - to tablice "ściągające" dane z formularza po zatwierdzeniu 

				// Standardowe użycie klasy wygląda następująco: 
				// $zmienioneDaneDoPods = new ZmienioneDaneDoPods('ekran_kasa',$_POST["godzina"],$_POST["nazwa_wydarzenia"],$_POST["id"]);

				if(count($godzina) == count($nazwa_wydarzenia) AND count($godzina) == count($id)){
				// jeśli ilość elementów we wszystkich trzech tablicach $godzina, $nazwa_wydarzenia, $id jest jednakowa (co powinno być formalnością) tworzona jest instancja klasy
					$this -> _nazwa_pods = $nazwa_pods; 
					$this -> _godzina = $godzina;
					$this -> _nazwa_wydarzenia = $nazwa_wydarzenia;
					$this -> _id = $id;
					$this -> _count = count($godzina);
				}
				else{
				// w przeciwnym wypadku wyświetlany jest komunikat i kod zostaje przerwany
					printf("Błąd! Nie udało się zainicjować klasy ZmienioneDane");
					exit();
				}
			}//function __construct

			public function saveAllElementsToPods(){
			// funkcja zapisujące dane z formularza (przechowywane w tablicach _godzina, _nazwa_wydarzenia, _id) do pods zdefiniowanego w _nazwa_pods

				for($i = 0; $i < $this->_count; $i++){

					$pod = pods( $this->_nazwa_pods, $this->_id[$i] ); 

					$data = array(
					    'godzina' 			=> esc_attr($this->_godzina[$i]),
					    'nazwa_wydarzenia' 	=> esc_attr($this->_nazwa_wydarzenia[$i])
					);

					$pod->save( $data ); 

				}

			}

		}//class ZmienioneDane

		// pobranie danych z formularza do zmiennej $zmienioneDaneDoPods
		$zmienioneDaneDoPods = new ZmienioneDaneDoPods('ekran_kasa',$_POST["godzina"],$_POST["nazwa_wydarzenia"],$_POST["id"]);
		// zapisanie danych w pods
		$zmienioneDaneDoPods->saveAllElementsToPods();

	}//if(isset( $_POST["zapisz"]))

	// POBIERANIE WPISÓW WYGENEROWANYCH NA EKRAN (z pods ekran_kasa)
	// Sortowanie odbywa się na podstawie wartośc pola kolejnosc!!!
							
	$params = array( 	'limit' => -1,
						'orderby'  => 'kolejnosc.meta_value');
	
	$pods = pods( 'ekran_kasa', $params );

	if ( $pods->total() > 0 ) {


		while ( $pods->fetch() ) {

			$nazwa_wydarzenia =  $pods->display('nazwa_wydarzenia');
			$godzina = $pods->display('godzina');
			$id = $pods->display('id');

			?>
			<tr>
				<!-- pole godziny: -->
				<td><?php printf('<input type="text" name="godzina[]" id="godzina" value="%s">',esc_attr($godzina))?></td>
				<!-- pole nazwy wydarzenia  -->
				<td><?php printf('<input type="text" name="nazwa_wydarzenia[]" id="nazwa_wydarzenia" value="%s">',esc_attr($nazwa_wydarzenia))?>
				<!-- ukryte pole id (niezbędne do zidentyfikowania, które elementy pods należy zapisać) -->
				<?php printf('<input type="hidden" name="id[]" id="id" value="%s">',esc_attr($id)); ?></td>
			</tr>
			<?php

	    }//while ( $pods->fetch() )   

	}//if ( $pods->total() > 0 )

	// POBIERANIE DOPISKU POD WPISAMI [aktualnie na ceny biletów] (z pods ekran_kasa_ustawienia) - później także ustawień "stylów" na ekran
	$params = array( 'limit' => -1);
	$pods = pods( 'ekran_kasa_ustawienia', $params );
	if(!empty($pods)){
		$dopisek = $pods->display('dopisek');
		printf('<tr><td colspan="2">');
		printf('<input type="text" name="dopisek" id="dopisek" value="%s">',esc_attr($dopisek));
		printf('</td></tr>');
	}//if(!empty($pods))


?>
</table><!-- koniec tabeli repertuaru -->

<!-- $params = array( 'limit' => -1);
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
		$pods->save( 'dzien_wygenerowany', $dzisiaj );  -->


    <input type="submit" name="zapisz" value="Zapisz" id="zapiszButton" title="" />
</form>

<!-- Podgląd ekranu o odpowiadającej rozdzielczości -->
<iframe src="http://www.kulturaolawa.nazwa.pl/testy_laboratorium/ekran/" width="1280" height="646" style="margin-left:0px"> <p>Podgląd ekranu</p> </iframe>