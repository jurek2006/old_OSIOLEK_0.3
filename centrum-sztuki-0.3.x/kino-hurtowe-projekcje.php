<?php
/*
Template Name: Kino - hurtowe projekcje
Description: Obsługuje stronę dodawania hurtowego projekcji filmowych stylu Centrum Sztuki w Oławie.
Tylko dla użytkowników zalogowanych (posiadających uprawnienia do publikacji postów). 

Skrypt w części zapisu projekcjido pods (funkcja zapiszProjekcje) sprawdza, czy zapis ten nie został odświeżony (ponowiony) - zapobiega to niechcianemu dodaniu np. podwójnie projekcji
W funkcji generowania formularza wyboru filmów (formularzWyboruFilmow) następuje usuwanie sesji i jej danych, żeby można było bez problemu zaczynać proces od nowa
*/

define("ILE_POL_PROJEKCJI",100); //określa ile pól na wpisanie projekcji będzie w formularzu
define("ILE_POL_WYBRANYCH_FILMOW",20); //określa ile pól na wybranie aktualnie dodanych filmów będzie w kroku 1 formularza

get_header(); ?>


	

<div id="main-wrap">
	<div id="main-container" class="clearfix">

    	<section id="content-container" class="column-9 hurtoweProjekcje">
		<?php
			if (current_user_can( 'publish_posts' )){	
			//jeśli jest zalogowany użytkownik o uprawnieniach do publikacji postów (co najmniej Autor) to wyświetlane jest zawartość strony
			
			//----------------------------------------------------------------------------------------------------------------------------------
			function czyWypelnioneJakiesPole($nazwaPola){
			//funkcja sprawdzająca, czy wprowadzono wartość przynajmniej jednego pola z generowanych automatycznie w formularzu (wartość 0 oznacza, że nie wypełniono)
			//chodzi o pola "tablicowe" np. <select name="sluchacz[]">
			//- $nazwaPola określa nazwę pól, np sluchacz
			//zwraca true gdy jest wypełnione przynajmniej jedno pole
			//false gdy nie wypełniono żadnego pola	
					
				if(isset($_POST[$nazwaPola]) && is_array($_POST[$nazwaPola])){
					foreach($_POST[$nazwaPola] as $pole){
						if($pole > 0)
							return true;
					}//foreach($_POST[$nazwaPola] as $pole)
				}//if(isset($_POST[$nazwaPola]) && is_array($_POST[$nazwaPola]))
						
				return false; //jeśli nie znaleziono żadnej wartośći w żadnym polu zwraca false
				
			}//czyWypelnioneJakiesPole($nazwaPola)
			//----------------------------------------------------------------------------------------------------------------------------------
			function pobierzWartosciPol($nazwaPola, $params = false){	
			//funkcja pobierająca wartości z generowanych automatycznie w formularzu
			//chodzi o pola "tablicowe" np. <select name="sluchacz[]">
			//- $nazwaPola określa nazwę pól, np sluchacz
			//funkcja traktuje wartości 0 jako puste i je pomija, zwraca tablicę tylko wybranych wartości innych niż 0
			//zwraca tablicę wartości tych pól
			//jeśli nie ma żadnego pola większego od 0 to zwraca pustą tablicę 
			
				$wybraneWartosciPol = array();
				if(isset($_POST[$nazwaPola]) && is_array($_POST[$nazwaPola])){
				//jeśli przekazane zostają 'pola tablicowe' następuje iteracja po każdym elemencie, do wynikowej tablicy dodawane są tylko wartości wybrane
				//większe od 0 (zrobione w ten sposób, żeby indeksy były po kolei
					foreach($_POST[$nazwaPola] as $pole){
						if($pole > 0)
							$wybraneWartosciPol[] = $pole;
					}//foreach($_POST[$nazwaPola] as $pole)
					
				}//if(isset($_POST[$nazwaPola]) && is_array($_POST[$nazwaPola]))
						
				return $wybraneWartosciPol; //jeśli nie znaleziono żadnej wartośći (większej od zera) w żadnym polu zwraca pustą tablicę
				
				
			}//function pobierzWartosciPol($nazwaPola)
			//----------------------------------------------------------------------------------------------------------------------------------
			function pobierzFilmy($zakresFilmow){
				$filmy = array();
				
				if(!isset($zakresFilmow)){
				//jeśli nie podano parametru $zakresFilmow to wstawiane jest standardowe, wyszukiwanie wszystkich filmów bez limitu
				
					$params = array('limit' => -1);
								
				}//if(!$params)
				else{
				//jeśli przekazano tablicę 
					
					$params = array('limit' => -1,
									'where' => 'ID IN ('.implode(",", $zakresFilmow).')'
									);
				}
							
				//get pods object

				$pods = pods( 'filmy', $params );

				if ( $pods->total() > 0 ) {
					while ( $pods->fetch() ) {
						$name =  $pods->display('name');
						$id = $pods->field('ID');
						$filmy[$id]  = $name;
					}//while ( $pods->fetch() )
				}//if ( $pods->total() > 0 )
							
				return $filmy;
			}//function pobierzFilmy()
			//----------------------------------------------------------------------------------------------------------------------------------
			function wypelnijOptions($tabela, $etykietaDlaNiewybranego = 'Wybierz', $valueWybranego = false){
			//funkcja generująca OPTIONS dla select (musi być wywoływana wewnątrz <select>)
			//opcje pobierane są z $tabela, gdzie klucz to vaule, a wartość to etykieta (text)
			//Na początku dodawana jest opcja "niewybrania" o wartości zero, której etykietę można nadać parametrem $etykietaDlaNiewybranego
			//Opcjonalny parametr $valueWybranego oznacza, która wartośc ma być wybrana na początku (ma mieć selected)
				
				$format = '<option value="%d">%s</option>';
				$formatSelect = '<option value="%d" selected>%s</option>';
				printf($format, 0, $etykietaDlaNiewybranego);
				
				foreach($tabela as $value => $text){
					
					if($valueWybranego != false && $value == $valueWybranego){
					//jeśli podano $valueWybranego (nie jest false) i $value aktualnej opcji jest taka sama jak $valueWybranego
					//dodanie opcji z select
						printf($formatSelect, $value, esc_attr($text));
					}
					else{
					//jeśli jest to normalna opcja, bez select
						printf($format, $value, esc_attr($text));
					}
					
				}//foreach($tabela as $id => $value)
				
			}//wypelnijOptions()
			//----------------------------------------------------------------------------------------------------------------------------------
			function formularzWyboruFilmow(){
			//wyświetla formularz wyboru filmów (krok 1)
			
			
					//---------------------------------------------------------------------			
					//Fragment niszczący sesję i dane sesyjne
					//W tym skrypcie są one używane do sprawdzenia, czy część za zapisem projekcji do pods (funkcja zapiszProjekcje) nie została odświeżona
					//zapobiega to niechcianemu dodaniu np. podwójnie projekcji
					//Poniższy fragment niszczy sesję i jej dane - występuje w formularzu wyboru filmów - ponieważ jest to krok 1 całego procesu - więc wiadomo, że dodawanie zaczeło się od nowa
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
					//---------------------------------------------------------------------
				
					$filmy = pobierzFilmy();
							
					?>
					
					<h2>Krok 1 - wybierz filmy dla których będą dodawane projekcje</h2>

						<p>Filmy, dla których będą dodawane projekcje:</p>
						<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
							
							<!--Stworzenie pól select do wyboru filmów w ilości określanej przez stałą ILE_POL_WYBRANYCH_FILMOW -->
							<?php for($i = 0; $i < ILE_POL_WYBRANYCH_FILMOW; $i++):?>
								<select name="film[]" id="film">
								<?php
									wypelnijOptions($filmy, 'Wybierz film'); //wypełnia selecta opcjami z tabeli filmy
								?>
								</select><br />
							<?php endfor; //for($i = 0; $i < ILE_POL_WYBRANYCH_FILMOW; $i++):?>
							
							<input type="submit" name="dalej" value="Dalej"/>
	
						</form>
						<hr />
					   
						
					
					<?php
				}//formularzWyboruFilmow()	
			//----------------------------------------------------------------------------------------------------------------------------------
			function formularzDodawaniaProjekcji($filmy, $wybraneProjekcje = false){
			//wyświetla formularz dodawania projekcji(krok 2)
			//- $filmy to tablica identyfikatorów filmów wybranych w kroku 1
			//- $wybraneProjekcje to tabluca gdzie:
			// $wybraneProjekcje['dane'] to tabela danych już wybranych wcześniej w formularzu - zatem na wejściu nasz formularz musi być nimi wypełniony
			//$wybraneProjekcje['error'] true/false - czy wystąpił error w tablicy
			//$wybraneProjekcje['warning'] true/false - czy wystąpił warning w tablicy
			?>
				<h2>Krok 2 - ustawianie projekcji</h2>
                <p>Jeśli dla danego wiersza nie wybrano daty projekcji (analogicznie dla daty publikacji) jest ona brana z wiersza wyżej<br />
Jeśli w wierszu nie wpisano ani ID sprzedaży - wiersz jest traktowany jako niewypełniony i ignorowany<br />
Projekcje należy wpisywać w kolejnych wierszach zaczynająć od pierwszego</p>

					<?php 
						//$filmy na wejściu to tablica identyfikatorów filmów wybranych w kroku 1
						//na wyjściu funkcji pobierzFilmy to tablica w formie $idFilmu => $tytułFilmu
						//tylko dla tych filmów będzie można dodać projekcje w kroku 2
                        $filmy = pobierzFilmy($filmy);
						
						//Sprawdzenie czy było ostrzeżenie w tablicy danych
						if($wybraneProjekcje['warning']){
							echo "<p class=\"komunikatOstrzezenia\">Ostrzeżenie - nie podano ID sprzedaży dla pól zaznaczonych na żółto</p>";
						}
						if($wybraneProjekcje['error']){
							echo "<p class=\"komunikatBledu\">Błąd - nie podano wymaganych danych dla pól zaznaczonych na czerwono</p>";
						}
							
					?>
                    
                    
                
                 	<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
                         
                       <?php           
					   //Dodawanie przycisków SPRAWDŹ, ZAPISZ, MIMO OSTRZEŻENIA, ZAPISZ w zależności od stanu wejściowego formularza
					   //SPRAWDŹ - jeśli nie przekazano żadnych danych wejściowych formularza (jest to pierwszy przebieg)
					   //lub pojawiło się ostrzeżenie, lub błąd
					   if($wybraneProjekcje == false || $wybraneProjekcje['warning'] || $wybraneProjekcje['error']){       
                       		?><input type="submit" name="sprawdz" value="Sprawdź"/><?php
					   }
					   
					   //ZAPISZ, MIMO OSTRZEŻENIA - jeśli wystąpiło ostrzeżenie, ale nie błąd
					   if($wybraneProjekcje['warning'] && !$wybraneProjekcje['error']){
						   ?><input type="submit" name="zapisz" value="Zapisz, mimo ostrzeżenia"/><?php
					   }
					   
					   //ZAPISZ - jeśli przekazano dane (nie jest to pusty formularz wejściowy) - nie wystąpił błąd ani ostrzeżenie
					   if($wybraneProjekcje != false && !$wybraneProjekcje['warning'] && !$wybraneProjekcje['error']){
						   ?><input type="submit" name="zapisz" value="Zapisz"/><?php
					   }
                        ?>
                        
                        <table class="dodajProjekcje">
                        <tr>
                        	<th>ID online</th>
                            <th>Film</th>
                            <th>2D/3D</th>
                            <th>Data projekcji</th>
                            <th>Godz</th>
                            <th>Data publikacji sprzedaży</th>
                        </tr>
                        
                        <?php for($i = 0; $i < ILE_POL_PROJEKCJI; $i++):?>
                        <!--Utworzenie zadanej w stałej ILE_POL_PROJEKCJI ilości pól na możliwość wpisania projekcji -->
                        
                        	
                        	<tr <?php if($i % 2 == 1) echo 'class="parzyste"'; /*jeśli jest to parzysty wiersz dodaje taką klasę*/?>>
                            
                            	<?php if(!$wybraneProjekcje){
									//jeśli nie przekazano parametru $wybraneProjekcje - czyli pusty formularz
									?>
                                
                                    <td><input type="text" name="id_online[]" pattern="[0-9]{3,4}" size="4" maxlength="4"></td>
                                    
                                    <td><select name="film_projekcji[]" id="film_projekcji">
                                    <?php
                                        wypelnijOptions($filmy, 'Wybierz film'); //wypełnia selecta opcjami z tabeli filmy
                                    ?>
                                    </select></td>
                                    
                                    <td><select name="format[]">
                                        <option value=""></option>
                                        <option value="3D">3D</option>
                                    </select></td>
                                
                                    <td><input type="date" name="data_projekcji[]"></td>
                                    <td><input type="time" name="czas_projekcji[]"></td>
                                    
                                    <td><input type="date" name="data_publikacji[]"></td>
                                
                                <?php }//if(!$wybraneProjekcje)
									else{
									//przekazano parametr $wybraneProjekcje - czyli należy wypełnić formularz wartościami początkowymi 
								?>
                                    
                                    <td <?php if($wybraneProjekcje['dane'][$i]['id_onlineWARNING']) echo 'class="warning"'?>>
                                    <!--Jeśli pole id_onlineWARNING jest TRUE to należy nadać komórce klasę warning-->
                                    <input type="text" name="id_online[]" pattern="[0-9]{3,4}" size="4" maxlength="4" value="<?php echo $wybraneProjekcje['dane'][$i]['id_online']?>"></td>
                                    
                                    <td><select name="film_projekcji[]" id="film_projekcji">
                                    <?php
                                        wypelnijOptions($filmy, 'Wybierz film', $wybraneProjekcje['dane'][$i]['film_projekcji']); //wypełnia selecta opcjami z tabeli filmy
                                    ?>
                                    </select></td>
                                    
                                    <td><select name="format[]">
                                    	<!--Jeśli dla danego wiersza wybrano format 3D to dodawany jest do tej opcji selected-->
                                    	<?php if($wybraneProjekcje['dane'][$i]['format'] == '3D'){?>
                                        		<option value=""></option>
                                       			<option value="3D" selected>3D</option>
                                        <?php }//if($wybraneProjekcje['dane'][$i]['format'] == '3D')
										else{?>
                                        <option value=""></option>
                                        <option value="3D">3D</option>
                                        <?php }//else od if($wybraneProjekcje['dane'][$i]['format'] == '3D')?>
                                    </select></td>
                                
                                    <td <?php if($wybraneProjekcje['dane'][$i]['data_projekcjiERROR']) echo 'class="error"'?>>
                                    <!--Jeśli pole data_projekcjiERROR jest TRUE to należy nadać komórce klasę error-->
                                    <input type="date" name="data_projekcji[]" value="<?php echo $wybraneProjekcje['dane'][$i]['data_projekcji']?>">									</td>
                                    
                                    <td <?php if($wybraneProjekcje['dane'][$i]['czas_projekcjiERROR']) echo 'class="error"'?>>
                                    <!--Jeśli pole czas_projekcjiERROR jest TRUE to należy nadać komórce klasę error-->
                                    <input type="time" name="czas_projekcji[]" value="<?php echo $wybraneProjekcje['dane'][$i]['czas_projekcji']?>"></td>
                                    
                                    <td <?php if($wybraneProjekcje['dane'][$i]['data_publikacjiERROR']) echo 'class="error"'?>>
                                    <!--Jeśli pole data_publikacjiERROR jest TRUE to należy nadać komórce klasę error-->
                                    <input type="date" name="data_publikacji[]" value="<?php echo $wybraneProjekcje['dane'][$i]['data_publikacji']?>"></td>
                                    
                                    
                                <?php }//else od if(!$wybraneProjekcje)?>
                            </tr>
                        <?php endfor; //for($i = 0; $i < ILE_POL_PROJEKCJI; $i++):?>
                        </table>
                        
                        <?php
						//przepisanie tablicy wybranych filmów do "pola tablicowego" hidden "film"
						//gdyby filmy wybrane w pierwszym kroku były potrzebne w kolejnym otworzeniu formularza
						foreach($filmy as $value => $title){
							echo '<input type="hidden" name="film[]" value="'.$value.'" />';
						}//foreach($filmy as $value => $title)
						?>
                        
                    	<input type="submit" name="sprawdz" value="Sprawdź"/>
                    </form>
                    <?php
			}//function formularzDodawaniaProjekcji()
			//----------------------------------------------------------------------------------------------------------------------------------
			function przepiszDoTablicyProjekcji(){
			//przepisuje do jednej tablicy dane pobrane z formularza dodawania projekcji
			//jeśli dla danego wiersza $film było 0 (nie wybrany film, to pomija ten wiersz)
				//pobranie danych z formularza
				$id_online = $_POST["id_online"];
				$film_projekcji = $_POST["film_projekcji"];
				$format = $_POST["format"];
				$data_projekcji = $_POST["data_projekcji"];
				$czas_projekcji = $_POST["czas_projekcji"];
				$data_publikacji = $_POST["data_publikacji"];
			
				$projekcje = array();
				
				for($i = 0; $i < count($film_projekcji); $i++){
					if($film_projekcji[$i] > 0){
						
						if($i > 0){
							
							if(empty($data_projekcji[$i]))
							//jeśli dla danego wiersza - poza pierwszym[0] nie podano daty projecji, to pobierana jest z projekcji wyżej
								$data_projekcji[$i] = $data_projekcji[$i-1];
								
							if(empty($data_publikacji[$i]))
								$data_publikacji[$i] = $data_publikacji[$i-1];
							//jeśli dla danego wiersza - poza pierwszym[0] nie podano daty publikacji, to pobierana jest z projekcji wyżej
						}
						
						$projekcje[] = array(	'id_online' => $id_online[$i],
												'film_projekcji' => $film_projekcji[$i],
												'format' => $format[$i],
												'data_projekcji' => $data_projekcji[$i],
												'czas_projekcji' => $czas_projekcji[$i],
												'data_publikacji' => $data_publikacji[$i],
											);
					}
				}//for($i = 0; $i < count($id_online); $i++)
				return $projekcje; 
			}//przepiszDoTabeli($id_online, $film, $format, $data_projekcji, $czas_projekcji, $data_publikacji)
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
					
					if(empty($projekcje[$i]['data_publikacji']) || !validateDate($projekcje[$i]['data_publikacji'], 'Y-m-d')){
					//jeśli nie podano daty publikacji to włączana jest ogólna flaga error i dodawany jest error do pola
						$error = true;
						$projekcje[$i]['data_publikacjiERROR'] = true;
					}
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
					
					$data = array(	'name' => $projekcja['data_projekcji'].' - '.$projekcja['czas_projekcji'].' - '.get_the_title($projekcja['film_projekcji'])." ".$projekcja['format'],
									'film' => $projekcja['film_projekcji'],
									'termin_projekcji' => $projekcja['data_projekcji'].' '.$projekcja['czas_projekcji'],
									'2d3d' => $czy3D,
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
							echo "<p class=\"komunikatSukcesu\">Dodano projekcję ".$projekcja['data_projekcji']." ".$projekcja['czas_projekcji']." - ".get_the_title($projekcja['film_projekcji'])." ".$projekcja['format']."</p>";
							//jeśli projekcja została dodana poprawnie, jej dane są dodawane do tabeli $dodaneProjekcje, żeby poniżej dokonać sprawdzenia
							$dodaneProjekcje[] = $projekcja;
							/*echo '<iframe src="http://www.systembiletowy.pl/cso/admin.php/sbSetupRepertoire?repertoire='.$projekcja['id_online'].'" width="1140" height="100" style="margin-left:0px"> <a href="http://www.systembiletowy.pl/cso/" title="System biletowy">System biletowy</a> </iframe>';*/
						}
						else{
							echo "<p class=\"komunikatBledu\">Błąd dodawania projekcji ".$projekcja['data_projekcji']." ".$projekcja['czas_projekcji']." - ".get_the_title($projekcja['film_projekcji'])." ".$projekcja['format']."</p>";
						}
					}//foreach($projekcje['dane'] as $projekcja)
					
					//------------część odpowiadająca za sprawdzenie zgodności danych wprowadzonych projekcji z systemem biletowym
					//identyfikatora, daty i godziny projekcji
					
					foreach($dodaneProjekcje as $projekcja){
						$format = '<div class="sprawdzanaProjekcja">
						<iframe src="http://www.systembiletowy.pl/cso/admin.php/sbSetupRepertoire?repertoire=%s" width="1140" height="100" style="margin-left:0px"> <a href="http://www.systembiletowy.pl/cso/" title="System biletowy">System biletowy</a> </iframe><br>
						<p>Na stronie: <span>%s, %s %s </span></p>
						</div><hr class="sprawdzanaProjekcja">';
						echo sprintf($format, $projekcja['id_online'], get_the_title($projekcja['film_projekcji'])." ".$projekcja['format'], zamienDateNaTekst($projekcja['data_projekcji']), $projekcja['czas_projekcji']);
					}//foreach($dodaneProjekcje as $sprawdzanaProjekcja)
					
				}//if(!$projekcje['error'])
			
			}//function zapiszProjekcje($projekcje)
			//----------------------------------------------------------------------------------------------------------------------------------
				
				?><h1>Dodawanie projekcji</h1>
                <p><strong>Uwaga! Strona działa poprawnie tylko w przeglądarkach: Chrome, Opera</strong></p>
				<?php
				
				if(isset( $_POST["dalej"]) && czyWypelnioneJakiesPole("film")){
				//------------Jeśli kliknięto "dalej" w kroku 1 - wyboru filmów do dodawania i wybrano jakiś film
					
					formularzDodawaniaProjekcji(pobierzWartosciPol("film"));
				
				}//if(isset( $_POST["dalej"]) && czyWypelnioneJakiesPole("film"))
				
				elseif(isset( $_POST["dalej"])){
				//------------Jeśli kliknięto "dalej" w kroku 1 ale nie wybrano żadnego filmu
				
					echo "<p class=\"komunikatBledu\">Nie wybrano żadnego filmu - wybierz przynajmniej jeden</p>";
					formularzWyboruFilmow();
					
				}//elseif(isset( $_POST["dalej"]))
				
				elseif(isset( $_POST["sprawdz"])){
				//Jeśli zatwierdzono krok 2 przyciskiem "Sprawdź"

					//Przepisanie tabeli formularza do tablicy
					?>
                        
                        <?php
						//przepisanie danych z formularza do tablicy
						$projekcje = przepiszDoTablicyProjekcji(); 
						//dokonanie walidacji - zwraca tablicę, gdzie element ['dane'] to tablica projekcji, ['error'] - error dla całej tablicy, ['warning'] - warning dla całej tablicy
						$projekcje = walidujTabliceProjekcji($projekcje);
						
						formularzDodawaniaProjekcji(pobierzWartosciPol("film"), $projekcje);

				}
				
				elseif(isset( $_POST["zapisz"])){
				//jeśli kliknięto przycisk Zapisz albo Zapisz, mimo ostrzeżeń
				
					//przepisanie danych z formularza do tablicy
					$projekcje = przepiszDoTablicyProjekcji(); 
					//dokonanie walidacji - zwraca tablicę, gdzie element ['dane'] to tablica projekcji, ['error'] - error dla całej tablicy, ['warning'] - warning dla całej tablicy
					//walidacja jest na wszelki wypadek - jeśli ktoś zmienił jakieś dane po wyświetleniu formularza ze sprawdzonymi danymi
					$projekcje = walidujTabliceProjekcji($projekcje);
					if(!$projekcje['error']){
					//jeśli nie został zgłoszony error następuje próba zapisu projekcji w pods
						zapiszProjekcje($projekcje);
					}
					
				
				}//elseif(isset( $_POST["zapisz"]))
				
				else{
				//------------Jeśli nie kliknięto "dalej" w kroku 1 (ani "sprawdź" ani "zapisz" w kroku 2) - czyli jest to krok 1
				//pobranie listy filmów - które będą później wstawiane jako opcje do select
				
					formularzWyboruFilmow();
					
				}//else od if(isset( $_POST["dalej"])):
			}//if (current_user_can( 'publish_posts' ))
			else{
				
				echo "<h1>Przepraszamy, nie masz uprawnień do tej strony.</h1>";
				
			}//else - if (current_user_can( 'publish_posts' ))?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>