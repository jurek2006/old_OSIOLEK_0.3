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

		// ------ WPISY FILMÓW -------
		// pobranie danych (tabeli wpisów filmów) z formularza do zmiennej $zmienioneDaneDoPods
		$zmienioneDaneDoPods = new ZmienioneDaneDoPods('ekran_kasa',$_POST["godzina"],$_POST["nazwa_wydarzenia"],$_POST["id"]);
		// zapisanie (tabeli wpisów filmów) danych w pods
		$zmienioneDaneDoPods->saveAllElementsToPods();

		// ----- USTAWIENIA - czyli dopisek i wielkości czcionki


		$params = array( 'limit' => -1);
		$pods = pods( 'ekran_kasa_ustawienia', $params );
		if(!empty($pods)){
			$pods->save( 'dopisek', $_POST["dopisek"] );
			$pods->save( 'filmy_font_size', $_POST["filmy_font_size"] );
			$pods->save( 'dopisek_font_size', $_POST["dopisek_font_size"] );
		}//if(!empty($pods))

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
				<td><?php printf('<input type="text" name="godzina[]" id="godzina" size="5" value="%s">',esc_attr($godzina))?></td>
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
		$filmy_font_size = $pods->display('filmy_font_size');
		$dopisek_font_size = $pods->display('dopisek_font_size'); 

		printf('<tr><td colspan="2">');
		printf('Dopisek: <input type="text" name="dopisek" id="dopisek" size="40" value="%s">',esc_attr($dopisek));
		printf('</td></tr>');

		// pole formularza odpowiadające za wielkość czcionki w tabeli filmów - z użyciem slidera jQueryUI (jego obsługa w skrypcie obslugaSlidera)
		printf('<tr><td colspan="2">');
		printf('Wielkość czcionki filmów: <input type="text" id="input_filmy_font_size" name="filmy_font_size" size="4" value="%s">',esc_attr($filmy_font_size)); 
		printf('<div id="slider_filmy_font_size"></div>');
		printf('</td></tr>');
		?>
			<script type="text/javascript">
				obslugaSlidera("#slider_filmy_font_size", "#input_filmy_font_size", 100, 1000, 10);
			</script>
		<?php

		// pole formularza odpowiadające za wielkość czcionki dopisku pod tabelą - z użyciem slidera jQueryUI (jego obsługa w skrypcie obslugaSlidera)
		printf('<tr><td colspan="2">');
		printf('Wielkość czcionki dopisku: <input type="text" id="input_dopisek_font_size" name="dopisek_font_size" size="4" value="%s">',esc_attr($dopisek_font_size)); 
		printf('<div id="slider_dopisek_font_size"></div>');
		printf('</td></tr>');
		?>
			<script type="text/javascript">
				obslugaSlidera("#slider_dopisek_font_size", "#input_dopisek_font_size", 100, 1000, 10);
			</script>
		<?php
		

	}//if(!empty($pods))


?>
</table><!-- koniec tabeli repertuaru -->

    <input type="submit" name="zapisz" value="Zapisz" id="zapiszButton" title="" />
</form>

<!-- Podgląd ekranu o odpowiadającej rozdzielczości -->
<iframe style="overflow:hidden" scrolling="no" src="<?php echo home_url()?>/ekran/" width="1280" height="646" style="margin-left:0px"> <p>Podgląd ekranu</p> </iframe>



