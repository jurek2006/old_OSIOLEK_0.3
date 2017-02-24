<h1>Ustawienia ekranu repertuaru w kasie</h1>

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

			public function __construct($nazwa_pods, $godzina, $nazwa_wydarzenia, $id, $komentarz){
				// paramatry:
				// - $nazwa_pods - definiuję nazwę pods do której zapisywane są dane (standardowo tutaj ekran_kasa)
				// - $godzina, $nazwa_wydarzenia, $id, $komentarz - to tablice "ściągające" dane z formularza po zatwierdzeniu 

				// Standardowe użycie klasy wygląda następująco: 
				// $zmienioneDaneDoPods = new ZmienioneDaneDoPods('ekran_kasa',$_POST["godzina"],$_POST["nazwa_wydarzenia"],$_POST["id"], $_POST["komentarz"]);

				if(count($godzina) == count($nazwa_wydarzenia) AND count($godzina) == count($id) AND count($godzina) == count($komentarz)){
				// jeśli ilość elementów we wszystkich czterech tablicach $godzina, $nazwa_wydarzenia, $id, $komentarz jest jednakowa (co powinno być formalnością) tworzona jest instancja klasy
					$this -> _nazwa_pods = $nazwa_pods; 
					$this -> _godzina = $godzina;
					$this -> _nazwa_wydarzenia = $nazwa_wydarzenia;
					$this -> _id = $id;
					$this -> _komentarz = $komentarz;
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
					    'nazwa_wydarzenia' 	=> esc_attr($this->_nazwa_wydarzenia[$i]),
					    'komentarz' 	=> esc_attr($this->_komentarz[$i])
					);

					$pod->save( $data ); 

				}

			}

		}//class ZmienioneDane

		// ------ WPISY FILMÓW -------
		// pobranie danych (tabeli wpisów filmów) z formularza do zmiennej $zmienioneDaneDoPods
		$zmienioneDaneDoPods = new ZmienioneDaneDoPods('ekran_kasa', $_POST["godzina"], $_POST["nazwa_wydarzenia"], $_POST["id"], $_POST["komentarz"]);
		// zapisanie (tabeli wpisów filmów) danych w pods
		$zmienioneDaneDoPods->saveAllElementsToPods();

		// ----- USTAWIENIA - czyli dopisek, dopisek2 i wielkości czcionki


		$params = array( 'limit' => -1);
		$pods = pods( 'ekran_kasa_ustawienia', $params );

		if(!empty($pods)){
			$pods->save( 'dopisek', $_POST["dopisek"] );
			$pods->save( 'dopisek2', $_POST["dopisek2"] );
			$pods->save( 'filmy_font_size', $_POST["filmy_font_size"] );
			$pods->save( 'dopisek_font_size', $_POST["dopisek_font_size"] );
			$pods->save( 'komentarze_font_size', $_POST["komentarze_font_size"] );
			// checkbox:
			if(isset($_POST["wyswietlaj_komentarze"])){
				$pods->save( 'wyswietlaj_komentarze', 'tak' );
			}
			else{
				$pods->save( 'wyswietlaj_komentarze', 'nie' );
			}
		}//if(!empty($pods))

	}//if(isset( $_POST["zapisz"]))

	?>
	<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
	<!-- formularz edycji wpisów na stronę ekranu repertuaru w kasie -->

		<div id="miejsce_na_pasek_kontrolny">
			<!-- miejsce gdzie jQuery przenosi pasek_kontrolny -->
		</div>

		<table class="tabela"><!-- początek tabeli repertuaru -->
				<colgroup>
				    <col class="godziny" />
				    <col/>
				    <col/>
				</colgroup>

	<?php

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
			$komentarz = $pods->display('komentarz');

			?>
			<tr>
				<!-- pole godziny: -->
				<td><?php printf('<input type="text" name="godzina[]" id="godzina" size="5" value="%s">',esc_attr($godzina))?></td>
				<!-- pole nazwy wydarzenia  -->
				<td><?php printf('<input type="text" name="nazwa_wydarzenia[]" id="nazwa_wydarzenia" value="%s">',esc_attr($nazwa_wydarzenia))?>
				<!-- ukryte pole id (niezbędne do zidentyfikowania, które elementy pods należy zapisać) -->
				<?php printf('<input type="hidden" name="id[]" id="id" value="%s">',esc_attr($id)); ?></td>
				<!-- pole komentarza: -->
				<td><?php printf('<input type="text" name="komentarz[]" class="komentarz" id="komentarz" value="%s">',esc_attr($komentarz))?></td>
			</tr>
			<?php

	    }//while ( $pods->fetch() )   

	}//if ( $pods->total() > 0 )

	// POBIERANIE DOPISKÓW POD WPISAMI [aktualnie na ceny biletów] (z pods ekran_kasa_ustawienia) - później także ustawień "stylów" na ekran
	$params = array( 'limit' => -1);
	$pods = pods( 'ekran_kasa_ustawienia', $params );
	if(!empty($pods)){
		$wyswietlaj_komentarze = $pods->field('wyswietlaj_komentarze');
		$dopisek = $pods->display('dopisek');
		$dopisek2 = $pods->display('dopisek2');
		$filmy_font_size = $pods->display('filmy_font_size');
		$dopisek_font_size = $pods->display('dopisek_font_size'); 
		$komentarze_font_size = $pods->display('komentarze_font_size'); 

		// wyświetlanie checkboxa
		printf('<tr><td colspan="2">Wyświetlaj komentarze filmów:</td>');
		echo '<td><input name="wyswietlaj_komentarze" id="wyswietlaj_komentarze" type="checkbox" value="1"';
		if($wyswietlaj_komentarze){
				echo ' checked';
			}
		printf('></td></tr>');

		?>
		<script type="text/javascript">
			// skrypt obsługi dodatkowej checkbox
			function wlacz_wylacz_komentarz(){

				if($("#wyswietlaj_komentarze").is(':checked')){
					console.log("Ready checked");

					$(".komentarz").prop('readonly', false).focus(function(){
						$(this).blur();
					});
				}
				else{
					$(".komentarz").prop('readonly', true).focus(function(){
						$(this).blur();
					});	
				}
			}
			// powyższa funkcja obsługi zdarzenia przypisana jest do zdarzenia zmiany wartości chechbox
			// oraz do wczytania strony
			// jeśli checkbox wyswietlaj_komentarze jest zaznaczony (a jest gdy pole wyswietlaj_komentarze jest true) to blokowane są pola komentarz
			$(document).ready(wlacz_wylacz_komentarz);
			$("#wyswietlaj_komentarze").change(wlacz_wylacz_komentarz);

		</script>
		<?php

		printf('<tr><td>Dopisek1:</td>');
		printf('<td colspan="2"><input style="width: 100%%" type="text" name="dopisek" id="dopisek" size="40" value="%s">',esc_attr($dopisek));
		printf('</td></tr>');

		printf('<tr><td>Dopisek2:</td>');
		printf('<td colspan="2"><input style="width: 100%%" type="text" name="dopisek2" id="dopisek2" size="40" value="%s">',esc_attr($dopisek2));
		printf('</td></tr>');

		// pole formularza odpowiadające za wielkość czcionki dopisku pod tabelą - z użyciem slidera jQueryUI (jego obsługa w skrypcie obslugaSlidera) 
		// określane w % względem wielkości czcionki filmów
		printf('<tr><td colspan="3">');
		printf('Wielkość czcionki dopisku: <input type="text" id="input_dopisek_font_size" name="dopisek_font_size" size="4" value="%s">%%',esc_attr($dopisek_font_size)); 
		printf('<div id="slider_dopisek_font_size"></div>');
		printf('</td></tr>');
		?>
			<script type="text/javascript">
				obslugaSlidera("#slider_dopisek_font_size", "#input_dopisek_font_size", 1, 100, 1);
			</script>
		<?php

		// pole formularza odpowiadające za wielkość czcionki komentarzy projekcji w tabeli - z użyciem slidera jQueryUI (jego obsługa w skrypcie obslugaSlidera) 
		// określane w % względem wielkości czcionki filmów
		printf('<tr><td colspan="3">');
		printf('Wielkość czcionki komentarzy: <input type="text" id="input_komentarze_font_size" name="komentarze_font_size" size="4" value="%s">%%',esc_attr($komentarze_font_size)); 
		printf('<div id="slider_komentarze_font_size"></div>');
		printf('</td></tr>');
		?>
			<script type="text/javascript">
				obslugaSlidera("#slider_komentarze_font_size", "#input_komentarze_font_size", 1, 100, 1);
			</script>
		<?php
		
		

	}//if(!empty($pods))
	?>
	</table> <!-- koniec tabeli repertuaru -->
	<script type="text/javascript">
				$(".tabela").hide(); //ukrycie tabeli na początku działania strony
	</script>
	<?php

	if(!empty($pods)){
		// pole formularza odpowiadające za wielkość czcionki w tabeli filmów - z użyciem slidera jQueryUI (jego obsługa w skrypcie obslugaSlidera)
		?>
		<div id="pasek_kontrolny" style="background-color: #CCFFCC; padding: 1em; width: 100%; margin: 2em 0;">
			<div style="float: left;">
				<?php printf('Wielkość czcionki filmów: <input type="text" id="input_filmy_font_size" name="filmy_font_size" size="4" value="%s">',esc_attr($filmy_font_size)); ?>
				<div id="slider_filmy_font_size"></div>
			
		
				<script type="text/javascript">
					obslugaSlidera("#slider_filmy_font_size", "#input_filmy_font_size", 100, 1000, 10);
				</script>
			</div>
			<div>
			<!-- przycisk "Zapisz" -->
			<input type="submit" name="zapisz" value="Zapisz" id="zapiszButton" title="" />
			<input type="button" name="szczegoly" value="Pokaż/ukryj szczegóły" id="szczegolyButton" title="" />
			<script type="text/javascript">
				obslugaButtonToggle("#szczegolyButton",'.tabela',"Ukryj szczegóły", "Pokaż szczegóły");
			</script>
			</div>
		</div>
		<script type="text/javascript">
			// przeniesienie całego #pasek_kontrolny nad tabelę
			$("#pasek_kontrolny").appendTo($('#miejsce_na_pasek_kontrolny'));
		</script>
		<?php
	}

?>

    
</form>

<!-- Podgląd ekranu o odpowiadającej rozdzielczości -->
<iframe style="overflow:hidden; margin-top: 1em" scrolling="no" src="<?php echo home_url()?>/ekran/" width="1280" height="646" style="margin-left:0px"> <p>Podgląd ekranu</p> </iframe>



