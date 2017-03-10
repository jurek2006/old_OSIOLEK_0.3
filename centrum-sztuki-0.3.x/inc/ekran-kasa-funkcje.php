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
			consoleLog("generowanie dla daty ".$data_generowania); //DIAGNOSTYCZNE

			// wygenerowanie nowej zawartości ekranu
			// generujDzien($data_generowania);

			// zapisanie $data_generowania jako wartości pola dzien_wygenerowany //DO PRZENIESIENIA!!!
			$pods_ekran_kasa_ustawienia->save( 'dzien_wygenerowany', $data_generowania ); 

			return $data_generowania;

		}
		else{
		// jeśli dla danej daty już jest wygenerowane - zwraca false
			consoleLog("Dzień ".$data_generowania." już był generowany"); //DIAGNOSTYCZNE
			return false;
		}

	}//if(!empty($pods_ekran_kasa_ustawienia))
	else{
	// jeśli nie udało się dostać do ustawień w POD_EKRAN_KASA_USTAWIENIA zwraca null (wcześniej wyświetla komunikat)
		echo "<b>Błąd pobierania ustawień ekran_kasa_ustawienia</b>";
		return NULL;
	}

}//function czyGenerowacEkranKasa()

?>