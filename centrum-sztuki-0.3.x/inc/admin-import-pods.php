<h1>Import PODS</h1>
<?php 
	if(esc_url( home_url()) == 'http://www.kultura.olawa.pl' && BLOKUJ_IMPORT_PODS){
	// jeśli motyw działa na stronie produkcyjnej, czyli kultura.olawa.pl i w function-settings BLOKUJ_IMPORT_PODS ustawiony jest na true to zablokowana jest możliwość korzystania z importu (nawet dla użytkowników posiadających odpowiednie uprawnienia)
		exit("Zablokowana możliwość importów na stronie produkcyjnej.");
	}

	// DALSZA CZĘŚĆ, JEŚLI NIE ZOSTAŁA ZABLOKOWANA MOŻLIWOŚĆ IMPORTU
	if(isset( $_POST["imp_filmy"])){
    //Jeśli kliknięto przycisk "Importuj filmy"
		if(isset($_POST["import"])){
			$import_json = $_POST["import"];
			// pozbycie się dodatkowych slashów
			$import_json = stripslashes_deep($import_json);

			$home_url_json = trim (json_encode(home_url()), '"' ); //zakodowanie home_url w json (z pozbyciem się średników na początku i końcu) - na potrzeby wyszukiwania i podmiany ścieżek

       		$import_json = str_replace(HOME_URL_ALIAS, $home_url_json, $import_json); //zamiana ścieżek zawierających string zdefiniowany w HOME_URL_ALIAS na home_url strony

			$import = json_decode( $import_json, true);
			if(is_null($import)){
				echo 'Nie udało się dekodować danych z JSON';
			}
			else{
			// gdy udało się dekodować dane z JSON

				if($import['rodzaj_danych'] == 'filmy'){
					unset($import['rodzaj_danych']); //usunięcie dodanego przeze mnie pola tablicy opisującego rodzaj danych
					echo '<br><br>';

					// DZIAŁAJĄCY IMPORT TESTOWY
					// Get the API
					$api = pods_api(PODS_FILMY);
					$data_do_importu = array();

					// Setup the data to import
					$data = array(
					    4801 => array(
					    	'post_title' => "Sztuka kochania",
					    	'post_name' => "sztuka-kochania"
					    ),
					    4827 => array(
					        'post_title' => "Sing",
					    	'post_name' => "sing"
					    ),
					    4829 => array(
					        'post_title' => "Konwój",
					    	'post_name' => "konwoj"
					    )
					);

					// // Run the import
					// $api->import( $data ); 

					// print_r($import);	 //TESTOWE

					foreach ($import as $key => $value) {
		             	
		             	$id = $key;

						$test_pod = pods( PODS_FILMY, $id );

						if ( $test_pod->exists() ) {
						    echo sprintf("<br>%s - %s: film istnieje", $key, $value['post_title']); 
						}
						else {
						    echo sprintf("<br>%s - %s: film nie istnieje", $key, $value['post_title']); 

						    // print_r($value);
						    $data_do_importu[$key] = $value;
						    // $api = pods_api(PODS_FILMY); 
						    // $api->import($value, true, 'php'); 

						    // $podsIMP = pods(PODS_FILMY); 
						    // $ids = $podsIMP->import($value, true );
			       //          printf("Dodano: ");
			       //          print_r($ids);
						}
		             	// print_r($value); //TESTOWE

		                // $podsIMP = pods(PODS_FILMY);
		                // $ids = $podsIMP->import($value);
		                // printf("Dodano: ");
		                // print_r($ids);
		            }//foreach ($import as $key => $value)
		            print_r($data_do_importu);
		            echo '<br><br>';
		            print_r($data);
		            // Run the import
					// $api->import( $data_do_importu ); 
				}
			}
			
		}
	}//if(isset( $_POST["imp_filmy"]))
	
?>
<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
	<textarea name="import" rows="10"  style="width: 100%"></textarea>
	<input type="submit" name="imp_filmy" value="Importuj filmy" class="button" />
</form>

<script type="text/javascript">
    $(".button").button();
</script>

