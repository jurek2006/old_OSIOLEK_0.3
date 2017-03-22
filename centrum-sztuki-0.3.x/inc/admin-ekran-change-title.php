<?php 
// =======================================================================================================
// --------------------- STRONA POMOCNICZA do admin-ekran-page dająca możliwość edycji skróconego tytułu filmu/wydarzenia na potrzeby ekranu repertuaru w kasie
// dostęp do niej realizowany jest ze strony admin-ekran-page (z odnośników w 'szczegółach') przekazujących jako event_id id filmu lub wydarzenia
// włączana za pomocą require_once
// =======================================================================================================
	if(isset($_POST["zapisz"])){
	// jeśli jest to zapisywanie formularza
		echo "Zapisywanie: " . $_POST["in_tytul_skrocony_ekran"] . ' - ' . $_POST["typWydarzenia"] . ' - ' . $_POST["event_id"] ;
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
				$typWydarzenia = "film";
				$typWydarzeniaOdm = "filmu";
				
			}
			else if($podsW->exists()){
			// jeżeli znaleziono event_id wśród wydarzeń, to pod $pods podstawiany jest pod tego wydarzenia
				$pods = $podsW;	
				$typWydarzenia = "wydarzenie";
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
						<input type="hidden" name="typWydarzenia" value="<?php echo $typWydarzenia; ?>" />
						<input type="submit" name="zapisz" value="Zapisz skrót" />
					</form>
					
				<?php
			}




		}
		else{
			echo "<h1>Dodanie skrótu tytułu filmu/wydarzenia</h1>";
			echo 'Błąd - nie przekazano żadnego event_id';
		}
	}//else - if(isset($_POST["zapisz"]))
?>





