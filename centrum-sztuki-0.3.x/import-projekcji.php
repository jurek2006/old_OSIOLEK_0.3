<?php
/*
Template Name: Kino - Import projekcji
Description: Obsługuje stronę importowania projekcji z systemu biletowego.
Tylko dla użytkowników zalogowanych (posiadających uprawnienia do publikacji postów). 

*/

get_header(); 
?>


	

<div id="main-wrap">
	<div id="main-container" class="clearfix">

    	<section id="content-container" class="column-12">
		<?php
			if (current_user_can( UPR_IMPORT_PROJEKCJI )){
			//jeśli jest zalogowany użytkownik o uprawnieniach zgodnych z wymaganymi do dostępu do tej strony
			//pobranie listy filmów wpisanych na stronie

				function przepiszDoTablicyProjekcji(){
				//przepisuje do jednej tablicy dane pobrane z formularza dodawania projekcji
				//jeśli dla danego wiersza $film było 0 (nie wybrany film, to pomija ten wiersz)
					//pobranie danych z formularza
					$id_online = $_POST["id_online"];
					$film_projekcji = $_POST["film_projekcji"];
					$format = $_POST["format"];
					$wersja_jezykowa = $_POST["wersja_jezykowa"];
					$data_projekcji = $_POST["data_projekcji"];
					$czas_projekcji = $_POST["czas_projekcji"];
					$data_publikacji = $_POST["data_publikacji"];
				
					$projekcje = array();
					
					for($i = 0; $i < count($film_projekcji); $i++){
						if($film_projekcji[$i] > 0){
							
							
							$projekcje[] = array(	'id_online' => $id_online[$i],
													'film_projekcji' => $film_projekcji[$i],
													'format' => $format[$i],
													'wersja_jezykowa' => $wersja_jezykowa[$i],
													'data_projekcji' => $data_projekcji[$i],
													'czas_projekcji' => $czas_projekcji[$i],
													'data_publikacji' => $data_publikacji[$i],
												);
						}
					}//for($i = 0; $i < count($id_online); $i++)
					return $projekcje; 
				}//przepiszDoTabeli()
				//----------------------------------------------------------------------------------------------------------------------------------
				function validateDate($date, $format = 'Y-m-d H:i:s'){
					$d = DateTime::createFromFormat($format, $date);
					return $d && $d->format($format) == $date;
				}
				//----------------------------------------------------------------------------------------------------------------------------------
				function walidujTabliceProjekcji($projekcje){
				//funkcja walidująca tablicę projekcji 
				//sprawdza czy podano datę i godzinę projekcji oraz datę publikacji (odnośnika do sprzedaży)
					$error = false;
					$warning = false;
						
					
					for($i = 0; $i < count($projekcje); $i++){
						
						if($projekcje[$i]['id_online'] < 1){
						//jeśli nie podano id online to włączana jest ogólna flaga warning i dodawany jest warning do pola
							$warning = true;
							$projekcje[$i]['id_onlineWARNING'] = true;
						}
						
						if(empty($projekcje[$i]['data_projekcji']) || !validateDate($projekcje[$i]['data_projekcji'], 'Y-m-d')){
						//jeśli nie podano daty projekcji to włączana jest ogólna flaga error i dodawany jest error do pola
							$error = true;
							$projekcje[$i]['data_projekcjiERROR'] = true;
						}
						
						if(empty($projekcje[$i]['czas_projekcji'])|| !validateDate($projekcje[$i]['czas_projekcji'], 'H:i')){
						//jeśli nie podano czasu projekcji to włączana jest ogólna flaga error i dodawany jest error do pola
							$error = true;
							$projekcje[$i]['czas_projekcjiERROR'] = true;
						}
						
						//data_publikacji może być pusta, więc nie musi być sprawdzana
					}
					
					return array(	'dane' => $projekcje,
									'error' => $error,
									'warning' => $warning);
					
				}//walidujTabliceProjekcji($projekcje)
				//----------------------------------------------------------------------------------------------------------------------------------
				function zapiszProjekcje($projekcje){
				//funkcja zapisująca projekcje do pods projekcje
				//- $projekcje to tablica gdzie:
				// $projekcje['dane'] to tabela danych już wybranych wcześniej w formularzu - zatem na wejściu nasz formularz musi być nimi wypełniony
				//$projekcje['error'] true/false - czy wystąpił error w tablicy
				//$projekcje['warning'] true/false - czy wystąpił warning w tablicy
				
					//sprawdzenie czy nie zgłoszono błędu w danych - jeśli nie, zaoisanie danych projekcji do pods
					if(!$projekcje['error']){
						
						//sprawdzanie czy krok z zapisem danych do pods jest ponawiany (odświeżany)
						//jeśli zmienna sesji 'refresh' jest true to oznacza, że funkcja zapisu była już uruchamiana
						//wówczas zmiennej $odswiezanie nadawana jest wartość true (wtedy funkcja różni się od normalnego przebiegu tylko tym, że nie dokonuje faktycznego zapisu do pods)
						//
						//jeśli zmienna sesji 'refresh' jest nie jest true to dostaje ona taką wartośc (na potrzeby kolejnego przebiegi - odświeżenia)
						//wówczas następuje normalny przebieg z zapisem
						session_start();
						if($_SESSION['refresh']){
							echo '<p class="komunikatOstrzezenia">Strona odświeżana, bez faktycznego, ponownego zapisu</p>';
							$odswiezanie = true;
						}
						else{
							$_SESSION['refresh'] = true;
						}
						
						foreach($projekcje['dane'] as $projekcja){ 
						
							$czy3D = false;
							if($projekcja['format'] == '3D'){
								$czy3D = true;
							}
							
							$data = array(	'name' => $projekcja['data_projekcji'].' - '.$projekcja['czas_projekcji'].' - '.get_the_title($projekcja['film_projekcji'])." ".$projekcja['format']." [".$projekcja['wersja_jezykowa']."]",
											'film' => $projekcja['film_projekcji'],
											'termin_projekcji' => $projekcja['data_projekcji'].' '.$projekcja['czas_projekcji'],
											'2d3d' => $czy3D,
											'wersja_jezykowa' => $projekcja['wersja_jezykowa'],
											'id_w_sprzedazy_online' => $projekcja['id_online'],
											'dzien_publikacji_odnosnika_do_biletow' => $projekcja['data_publikacji'],
											'godzina_publikacji_odnosnika_do_biletow' => '10:00',
											//'post_status' => 'publish'
										);
								
							//FAKTYCZNY ZAPIS DO PODS
							//jeśli jest to odświeżanie to nie następuje faktyczny, ponowny zapis do pods, tylko zwrócona wartość 1, żeby reszta wyświetlała się poprawnie
							if(!$odswiezanie){
								$new_id = pods( 'projekcje' )->add( $data );
							}
							else{
								$new_id = 1;
							}
							
							if($new_id > 0){
								echo "<p class=\"komunikatSukcesu\">Dodano projekcję ".$projekcja['data_projekcji']." ".$projekcja['czas_projekcji']." - ".get_the_title($projekcja['film_projekcji'])." ".$projekcja['format']." [".$projekcja['wersja_jezykowa']."]</p>";
								//jeśli projekcja została dodana poprawnie, jej dane są dodawane do tabeli $dodaneProjekcje, żeby poniżej dokonać sprawdzenia
								$dodaneProjekcje[] = $projekcja;
							}
							else{
								echo "<p class=\"komunikatBledu\">Błąd dodawania projekcji ".$projekcja['data_projekcji']." ".$projekcja['czas_projekcji']." - ".get_the_title($projekcja['film_projekcji'])." ".$projekcja['format']." [".$projekcja['wersja_jezykowa']."]</p>";
							}
						}//foreach($projekcje['dane'] as $projekcja)
						
						//------------część odpowiadająca za sprawdzenie zgodności danych wprowadzonych projekcji z systemem biletowym
						//identyfikatora, daty i godziny projekcji
						//sprawdzanie odbywa się tu "na oko" przez człowieka
						
						// foreach($dodaneProjekcje as $projekcja){
						// 	$format = '<div class="sprawdzanaProjekcja">
						// 	<iframe src="http://www.systembiletowy.pl/cso/admin.php/sbSetupRepertoire?repertoire=%s" width="1140" height="100" style="margin-left:0px"> <a href="http://www.systembiletowy.pl/cso/" title="System biletowy">System biletowy</a> </iframe><br>
						// 	<p>Na stronie: <span>%s, %s %s </span></p>
						// 	</div><hr class="sprawdzanaProjekcja">';
						// 	echo sprintf($format, $projekcja['id_online'], get_the_title($projekcja['film_projekcji'])." ".$projekcja['format'], zamienDateNaTekst($projekcja['data_projekcji']), $projekcja['czas_projekcji']);
						// }//foreach($dodaneProjekcje as $sprawdzanaProjekcja)
						
					}//if(!$projekcje['error'])
				
				}//function zapiszProjekcje($projekcje)
				//----------------------------------------------------------------------------------------------------------------------------------
				function zmienStatusPublikacji($zmienioneStatusy){
				//funkcja zmienia statusy publikacji postów (pods items) dla projekcji filmowych
					//$zmienioneStatusy to tablica w formie [671|future, 660|publish], gdzie przed | jest identyfikator postu (pods item) w wordpress, a po | status jaki ma być nadany

					global $tlumaczenieStatusuPostow; //zmienna globalna (zdef. w functions.php tłumaczeń statusów publikacji postów w WP)

					foreach($zmienioneStatusy as $doZmiany){
						$doZmiany = explode("|", $doZmiany);

						$identyfikator = $doZmiany[0];
						$nowyStatus = $doZmiany[1];

						$pod = pods( 'projekcje', $identyfikator ); 
						$sukces = $pod->save( 'post_status', $nowyStatus ); 

						//Jeśli zwrócono identyfikator zmienianego elementu, to znaczy, że udał się zapi
						if($sukces == $identyfikator){
							echo "<p class=\"komunikatSukcesu\">Zmieniono status publikacji projekcji ".get_the_title($identyfikator)." na ".$tlumaczenieStatusuPostow[$nowyStatus]."</p>";
						}
						else{
							echo "<p class=\"komunikatBledu\">Nie udało się zmienić statusu publikacji projekcji ".get_the_title($identyfikator)." na ".$tlumaczenieStatusuPostow[$nowyStatus]."</p>";
						}
					}

				}
				//----------------------------------------------------------------------------------------------------------------------------------

				function zmienDatyPoczSprzedazy($zmienioneDaty, $id_wordpress){
				//funkcja zmienia daty początku sprzedaży w postach (pods items) projekcji filmowych
					//$zmienioneDaty to tablica podanych nowych dat
					//$id_wordpress to tablica identyfikatorów postów, dla których te daty mają być nadane
					//czyli komplet danych do zmiany to $zmienioneDaty[$i] i $id_wordpress[$i]					

					if(count($zmienioneDaty) != count($id_wordpress)){
					//jeśli liczba elementów obu tablic jest różna - na pewno jest to błąd
						echo "<p class=\"komunikatBledu\">Nie zmieniono dat początku sprzedaży - błąd różnej długości tablic</p>";
					}
					else{
					//właściwy przebieg - jeśli tablice są równej długości
						for($i = 0; $i < count($zmienioneDaty); $i++){
						//iteracja po wszystkich elementach tablic $zmienioneDaty i $id_wordpress (mają taką samą liczbę elementów)
						//zmiana wartości pola dzien_publikacji_odnosnika_do_biletow dla każdej z danych projekcji

							$pod = pods( 'projekcje', $id_wordpress[$i] ); 
							$sukces = $pod->save( 'dzien_publikacji_odnosnika_do_biletow', $zmienioneDaty[$i] ); 

							//Jeśli zwrócono identyfikator zmienianego elementu, to znaczy, że udał się zapi
							if($sukces == $id_wordpress[$i]){
								echo "<p class=\"komunikatSukcesu\">Zmieniono dzień rozp. sprzed. dla projekcji ".get_the_title($id_wordpress[$i])." na ".$zmienioneDaty[$i]."</p>";
							}
							else{
								echo "<p class=\"komunikatBledu\">Nie udało się zmienić dnia rozp. sprzed. dla projekcji ".get_the_title($id_wordpress[$i])." na ".$zmienioneDaty[$i]."</p>";
							}
						}
					}

				}
				//----------------------------------------------------------------------------------------------------------------------------------



				if(isset( $_POST["zapisz"])){
				//Jeśli kliknięto przycisk "Zapisz" - czyli przesłano formularz projekcji do zapisania
					
					//ZAPISANIE NOWYCH PROJEKCJI
					//przepisanie danych z formularza do tablicy
					$projekcje = przepiszDoTablicyProjekcji(); 
					//dokonanie walidacji - zwraca tablicę, gdzie element ['dane'] to tablica projekcji, ['error'] - error dla całej tablicy, ['warning'] - warning dla całej tablicy
					//walidacja jest na wszelki wypadek - jeśli ktoś zmienił jakieś dane po wyświetleniu formularza ze sprawdzonymi danymi
					$projekcje = walidujTabliceProjekcji($projekcje);

					if(!$projekcje['error']){
					//jeśli nie został zgłoszony error następuje próba zapisu projekcji w pods
						zapiszProjekcje($projekcje);
					}

					//ZMIANA STATUSU PUBLIKACJI PROJEKCJI JUŻ ZAPISANYCH
					//Pobranie danych o zmienionych statusach
					$zmienioneStatusy = $_POST["zmienionyStatus"];
					zmienStatusPublikacji($zmienioneStatusy);

					//ZMIANA DATY POCZĄTKU SPRZEDAŻY PROJEKCJI JUŻ ZAPISANYCH
					//Pobranie danych o zmienionych datach

					$zmienioneDaty = $_POST["publikacja_biletow_dzien"];
					$id_wordpress = $_POST["id_wordpress"];
					zmienDatyPoczSprzedazy($zmienioneDaty, $id_wordpress);

					//Dodanie przycisku przejścia do "początku" działania narzędzia importu - czyli bez przekazania żadnych parametrów
					?>
					<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
		                <input type="submit" name="wroc_na_poczatek" value="Powrót do narzędzia importowania projekcji" />
		            </form>
		            <?php

				}
				else{
				//Jeśli otworzono stronę "standardowo" - a nie w wyniku przesyłania formularza projekcji do zapisania
					//=============================================================================================

					//USUNIĘCIE SESJI I DANYCH SESYJNYCH
					//W tym skrypcie są one używane do sprawdzenia, czy część za zapisem projekcji do pods (funkcja zapiszProjekcje) nie została odświeżona
					//zapobiega to niechcianemu dodaniu np. podwójnie projekcji
					//Poniższy fragment niszczy sesję i jej dane - występuje w formularzu wyboru filmów - ponieważ jest to krok 1 całego procesu - więc wiadomo, że dodawanie zaczeło się od nowa

					?>
					<script>
						var home_url = "<?php echo home_url();?>";
	                </script>
	                <?php

					session_start();
					// Usuń wszystkie zmienne sesyjne
					$_SESSION = array();
					
					// Jeśli pożądane jest zabicie sesji, usuń także ciasteczko sesyjne.
					// Uwaga: to usunie sesję, nie tylko dane sesji
					if (isset($_COOKIE[session_name()])) { 
					   setcookie(session_name(), '', time()-42000, '/'); 
					}
					
					// Na koniec zniszcz sesję
					session_destroy();
					

					//Pobranie listy wpisanych filmów z WWW i przekazanie ich jako zmienna WWW (JSON) filmyWWW do skryptu w visualticket_import.js
					//Pobrane filmy zapisywane są w formacie zgodnym ze źródłem autocomplete jQuery UI
					//czyli  ( [0] => Array ( [label] => Apartament [value] => apartament ) [1] => Array ( [label] => Czego dusza zapragnie [value] => czego-dusza-zapragnie ) [2] => Array ( [label] => Dar [value] => dar )
					$params = array( 'limit' => -1);			
			
					$pods = pods( 'filmy', $params );
					//loop through records
					if ( $pods->total() > 0 ) {
						//jeśli znaleziono filmy spełniające określone kryteria - następuje 
						
						while ( $pods->fetch() ) {
							
							$title = $pods->display('name');
							$slug = $pods->display('slug');
							$id = $pods->field('ID');
							
							$filmyWWW[] = Array( 'label' => $title, 'slug' => $slug, 'value' => $id);
							
						}//while ( $pods->fetch() )
						
					}//if ( $pods->total() > 0 )
					
					?>
	                <script>
						var filmyWWW = <?php echo json_encode($filmyWWW);?>
	                </script>
	                
					<?php
					//=============================================================================================
					//Pobranie listy wpisanych projekcji z WWW (także szkicy, nieopublikowanych i zaplanowanych do publikacji) i przekazanie ich jako zmienna WWW (JSON) projekcjeWWW do skryptu w visualticket_import.js
					//Pobierane są tylko projekcje przyszłe

					$datetime = new DateTime();
					$dzisiajDate = $datetime->format('Y-m-d');		

					$params = array( 	'limit' => -1,
											'where' => 'DATE( termin_projekcji.meta_value ) >= "'.$dzisiajDate.'" 
														AND (t.post_status = "draft" OR t.post_status = "publish" OR t.post_status = "pending" OR t.post_status = "future")',
											'orderby'  => 'termin_projekcji.meta_value');

					$pods = pods( 'projekcje', $params );
					//loop through records
					if ( $pods->total() > 0 ) {
						//jeśli znaleziono filmy spełniające określone kryteria - następuje 
						
						while ( $pods->fetch() ) {
							
							$title = $pods->display('name');						
							$id_w_sprzedazy_online = $pods->display('id_w_sprzedazy_online');
							$post_status = $pods->field('post_status');
							$tytul_filmu =  $pods->display('film');
							$q2d3d = $pods->field('2d3d');
							//Zamiana formatu 3d (true) i 2d (false) na etykiedy '3D' i pustą dla 2D
							if ($q2d3d == true) {
								$q2d3d = '3D';
							}
							else{
								$q2d3d = '';
							}

							$projekcja_wersja_jezykowa = $pods->display('wersja_jezykowa');
							$termin_projekcji = $pods->display('termin_projekcji');
							$termin_projekcji = explode(" ", $termin_projekcji); //rozdzielenie terminu projekcji na datę $termin_projekcji[0] i godzinę $termin_projekcji[1]

							$dzien_publikacji_odnosnika_do_biletow = $pods->display('dzien_publikacji_odnosnika_do_biletow');
							$godzina_publikacji_odnosnika_do_biletow = $pods->display('godzina_publikacji_odnosnika_do_biletow');

							$id_wordpress = $pods->field('ID'); //id projekcji według WordPress
							
							$projekcjeWWW[$id_w_sprzedazy_online] = array(	'tytul_filmu' => $tytul_filmu,
																			'data_terminu' => $termin_projekcji[0],
																			'czas_terminu' => $termin_projekcji[1],
																			'format2d3d' => $q2d3d,
																			'wersja_jezykowa' => $projekcja_wersja_jezykowa,
																			'publikacja_biletow_dzien' => $dzien_publikacji_odnosnika_do_biletow,
																			'publikacja_biletow_godzina' => $godzina_publikacji_odnosnika_do_biletow, 
																			'nazwa_projekcji' => $title, 
																			'status_publikacji' => $post_status,
																			'id_wordpress' => $id_wordpress
																			);

							
						}//while ( $pods->fetch() )
						
					}//if ( $pods->total() > 0 )
					
					?>
					<script>
						var projekcjeWWW = <?php echo json_encode($projekcjeWWW);?>;
						var tlumaczenieStatusuPostow = <?php echo json_encode($tlumaczenieStatusuPostow);?>; //przekazanie do skryptu także tablicy $tlumaczenieStatusuPostow (def. w functions.php)
	                </script>
	                <?php
					//=============================================================================================
					//Tabela filmów z VisualTicket - wypełniana w visualticket_import.js
					?>
	                <h2>Filmy:</h2>
	                <table id="filmy" class="dodajProjekcje">
	                </table>
					<?php
					//=============================================================================================
					//Tabela projekcji wczytanych z VisualTicket - wypełniana w visualticket_import.js
					?>
	                <h2>Projekcje:</h2>
	                <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
		                <table id="dodajProjekcje" class="dodajProjekcje">

		                </table>
		                <input type="submit" name="zapisz" value="Zapisz" id="zapiszButton" title="" />
		            </form>

		            <!-- Select umożliwiający zmianę statusu publikacji zapisanych (zaznaczonych) projekcji razem z buttonem zatwierdzającym -->
	            	<select name="statusyPublikacji">
						<option value="publish"><?php echo $tlumaczenieStatusuPostow["publish"] ?></option>
						<option value="draft"><?php echo $tlumaczenieStatusuPostow["draft"] ?></option>
						<option value="pending"><?php echo $tlumaczenieStatusuPostow["pending"] ?></option>
					</select>
					<input type="button" name="zmienStatusPublikacji" value="Zmień status zaznaczonych"/>
					<br>
					<!-- Pole tekstowe umożliwiające zmianę terminu publikacji zapisanych (zaznaczonych) projekcji -->
					<input type="text" name="dataPoczatekSprzedazy" id="dataPoczatekSprzedazy">
					<input type="button" name="zmienPoczatekSprzedazy" id="zmienPoczatekSprzedazy" value="Zmień datę publikacji sprzedaży zaznaczonych"/>
	                
	                <!--div - okna komunikaty używane w funkcji jQuery UI dialog()-->

	                <div id="wait" title="Czekaj...">
	                	<p>Trwa przetwarzanie danych</p>
	                    <p><strong>Czekaj aż okno zniknie</strong></p>
	                </div>

	                <div id="error" title="Przepraszamy">
	                	<p>Nie udało się pobrać danych z Systemu Biletowego</p>
	                    <p>Spróbuj ponownie za jakiś czas</p>
	                </div>

	                <!-- treść tooltip komunikatu dla przycisku zapisz -->
	                <script id="zapiszButtonTooltip" type="text/template">
	                	<h1>UWAGA!</h1>
	                	<p>Zapisane zostaną tylko projekcje o statusie <span class="wypelnione">"Gotowe do zapisu"</span> oraz <span class="zmienionaProjekcja">o zmienionej publikacji lub dacie początku sprzedaży</span></p>
	                </script>
	                
					<?php
				}
			}//if (current_user_can( UPR_KINO_ZARZADZANIE ))
			else{
				echo "<h1>Przepraszamy, nie masz uprawnień do tej strony.</h1>";
			}//else - if (current_user_can( UPR_KINO_ZARZADZANIE ))?>
		</section><!-- #content-container -->

	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->


<?php get_footer(); ?>