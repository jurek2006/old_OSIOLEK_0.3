<?php 
// =======================================================================================================
// --------------------- STRONA POMOCNICZA do admin-ekran-page dająca możliwość edycji skróconego tytułu filmu/wydarzenia na potrzeby ekranu repertuaru w kasie
// dostęp do niej realizowany jest ze strony admin-ekran-page (z odnośników w 'szczegółach') przekazujących jako event_id id filmu lub wydarzenia
// włączana za pomocą require_once
// =======================================================================================================
	if(isset($_POST["zapisz"])){
	// jeśli jest to zapisywanie formularza
	// $_POST["podName"] przekazuje nazwę pods na którym operujemy (to załatwia problem sprawdzania, czy jest to wydarzenie, czy film)
	// $_POST["event_id"] to identyfikator filmu/wydarzenia

		$tytul_skrocony_ekran = htmlspecialchars($_POST['in_tytul_skrocony_ekran']);
		$podName = $_POST['podName'];
		$event_id = $_POST["event_id"]; 

		$tytul_pelny = false; //tytuł pełny wydarzenia/filmu - na potrzeby gdy wpisano pusty tytuł skrócony w formularzu (i trzeba ustawić na ekranie pełny tytuł)
					
		// Zapisanie danych utworzonych powyżej w pods filmu lub wydarzeń 
		$pods = pods( $podName, $event_id ); 

		if($pods->exists()){
			$title = $pods->display('name');

			if($tytul_skrocony_ekran == $title)
			// sprawdzenie czy podany ciąg $tytul_skrocony_ekran nie jest identyczny z tytułem ($title) czyli nie zmieniono nic w tytule i bez sensu zapisywać go jako skrót
			// jeśli tak, to $tytul_skrocony_ekran ustawiany jest na pusty ciąg
			{
				$tytul_skrocony_ekran = '';
			}


			if(empty($tytul_skrocony_ekran)){
			// jeśli wpisano w formularzu pusty ciąg jako tytuł skrócony to zapamiętujemy tytuł pełny wydarzenia/filmu
				$tytul_pelny = $title;
			}
		}

		$pods->save( 'tytul_skrocony_ekran', $tytul_skrocony_ekran );

		
		// podmiana tytułu we wpisach w ekran_kasa (żeby nie trzeba było generować dla danego dnia od nowa)

		$params = array( 	'limit' => -1,
							'where'  => 'event_id.meta_value = "'.$event_id.'"');

		$pods = pods( POD_EKRAN_KASA, $params );

		if ( $pods->total() > 0 ) {

			while ( $pods->fetch() ) {

				if(!$tytul_pelny){
				// jeśli $tytul_pelny jest false (nie podano pustego ciągu w formularzu) to ustawia podany ciąg w formularzu jako nazwa wydarzenia
					$dane = array(
						'nazwa_wydarzenia' => $tytul_skrocony_ekran
					);
				}
				else{
				// jeśli $tytul_pelny nie jest false (podano pusty ciąg w formularzu) to ten tytuł jest zapisywany do wyświetlania jako nazwa wydarzenia
					$dane = array(
						'nazwa_wydarzenia' => $tytul_pelny
					);
				}
				
				$pods->save( $dane );
			}
		}

		// . (!$tytul_pelny) ? $tytul_skrocony_ekran : $tytul_pelny;
		printf("<br>Zapisano - film <strong>%s</strong> będzie się wyświetlał na ekranie jako <strong>%s</strong>.", $title, (!$tytul_pelny) ? $tytul_skrocony_ekran : $tytul_pelny);

		printf('<br><a href="%s">Wróć do zarządzania ekranem</a>', admin_url( 'admin.php?page=ekran_kasa') );


	}//if(isset($_POST["zapisz"]))
	else{
	// jeśli nie jest to zapisywanie formularza

	if(isset( $_GET["event_id"])){
			$event_id = $_GET["event_id"]; //pobranie id wydarzenia/filmu

			$pods = NULL;
			$podsF = pods( POD_FILMY, $event_id );
			$podsW = pods( POD_WYDARZENIA, $event_id );

			if($podsF->exists()){
			// jeżeli znaleziono event_id wśród filmów, to pod $pods podstawiany jest pod tego filmu
				$pods = $podsF;
				$podName = POD_FILMY;
				$typWydarzeniaOdm = "filmu";
				
			}
			else if($podsW->exists()){
			// jeżeli znaleziono event_id wśród wydarzeń, to pod $pods podstawiany jest pod tego wydarzenia
				$pods = $podsW;	
				$podName = POD_WYDARZENIA;
				$typWydarzeniaOdm = "wydarzenia";
			}
			else{
			// jeżeli nie znaleziono event_id ani wśród filmów ani wydarzeń, to wyświetla błąd i przerywa skrypt
				echo "<h1>Dodanie skrótu tytułu filmu/wydarzenia</h1>";
				echo 'Błąd - przekazano event_id, który nie jest ani filmem ani wydarzeniem';
				exit;
			}

			if($pods->exists()){
			// $pods zawiera w tym momencie dane filmu lub wydarzenia
				$title = $pods->display('name');
				$tytul_skrocony_ekran = $pods->display('tytul_skrocony_ekran');

				echo "<h1>Dodanie skrótu tytułu dla $typWydarzeniaOdm \"$title\"</h1>";

				?>
					<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
						<input type="text" name="in_tytul_skrocony_ekran" value="<?php echo (empty(!$tytul_skrocony_ekran) ? $tytul_skrocony_ekran : $title) ?>" size="30" maxlength="100" />
						<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
						<input type="hidden" name="podName" value="<?php echo $podName; ?>" />
						<input type="submit" name="zapisz" value="Zapisz skrót" />
					</form>

					<p>Aby wyświetlać na ekranie pełny tytuł filmu/wydarzenia należy zapisać skrót jako pusty (nie wpisywać nic)</p>
					
				<?php
			}




		}
		else{
			echo "<h1>Dodanie skrótu tytułu filmu/wydarzenia</h1>";
			echo 'Błąd - nie przekazano żadnego event_id';
		}
	}//else - if(isset($_POST["zapisz"]))
?>





