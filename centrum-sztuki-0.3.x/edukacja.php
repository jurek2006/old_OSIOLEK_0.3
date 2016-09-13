<?php
/*
Template Name: Edukacja
Description: Obsługuje stronę listy kursów i sekcji (edukacja) Centrum Sztuki w Oławie korzystając z pods kursy i grupy_kursowe oraz kategorie_kursow
Wyświetla kursy (z wybranej kategorii lub wszystkie, gdy żadnej nie wybrano) pogrupowane według kategorii oznaczonych jako główne w kategorie_kursow.
Grupowanie ma miejsce gdy wybrano do wyświetlenia wszystkie kursy lub jakąś kategorię (np. dla dorosłych) nie będącą kategorią główną.
*/


get_header(); ?>
<!--Fragment dla listy kursów - dodaje pasek wyświetlający jaka jest wybrana kategoria i umożliwiający jej zmianę -->

<?php
			if (!is_single()){
			//Na wszelki wypadek sprawdza czy to jest lista kursow
				
				//pobranie listy zdefiniowane_kategorie_kursow z pods kategorie
				//lista ta posłuży do sprawdzenia, czy wybrano którąś ze zdefiniowanych kategorii kursów
				//jeśli nie, traktowane jest to, jako wyświetlanie wszystkich wydarzeń
				//jeśli tak - tworzony jest pasek wyświetlający wybraną kategorię i umożliwiający jej zmianę
				$slug = pods_v('last','url');
				$zdefiniowane_kategorie_kursow = array();
				//tabela przechowująca nazwy kategorii, żeby wyświetlać je, zamiast slug'a
				$zdefiniowane_kategorie_kursow_nazwy = array();
				
				//Tablica $zdefiniowane_kategorie_glowne służy do zapamiętania slug'ów kategorii oznaczonych jako głowne 
				//Według nich są grupowane wyświetlane kursy na liście
				$zdefiniowane_kategorie_glowne = array();
				
				$params = array( 'limit' => -1);
				$pods1 = pods( 'kategorie_kursow', $params );
				if ( $pods1->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
                    while ( $pods1->fetch() ) {
						$slug = $pods1->display('slug');
						$zdefiniowane_kategorie_kursow[] = $slug;
						$nazwa = $pods1->display('name');
						$zdefiniowane_kategorie_kursow_nazwy[] = $nazwa;
						
						if($pods1->field('kategoria_glowna')){
						//sprawdzenie czy kategoria jest kategorią główną, jeśli tak to następuje jej dodanie do tablicy $zdefiniowane_kategorie_glowne
							$zdefiniowane_kategorie_glowne[] = $slug;
						}//if($pods1->field('kategoria_glowna'))
					}
				}
				
				
				$slug = pods_v('last','url'); //slug strony
				
				
				if(in_array($slug,$zdefiniowane_kategorie_kursow)){
					$params = array( 	'limit' => -1,
									'where'   => 'kategorie_kursu.slug LIKE "%'.$slug.'%"');
					//Pobranie nazwy wybranej kategorii - sprawdzamy jaki index w $zdefiniowane_kategorie_kursow ma wybrany $slug
					//A jako, że $zdefiniowane_kategorie_kursow_nazwy ma tą samą kolejność to pobierany z niego pole o tym indexie
					$nazwa_kategorii = $zdefiniowane_kategorie_kursow_nazwy[array_search($slug, $zdefiniowane_kategorie_kursow)];
									
				}
				else{
					//Wyświetlanie wszystkich wydarzeń
					$params = array( 	'limit' => -1 );
									
					//podstawienie pod slug na potrzeby wyświetlenia na pasku zmiany kategorii				
					$nazwa_kategorii = 'wszystkie'; 
				}
				
				//Stworzenie paska
					 	
					?>
                    	<div id="kategorie-wrap">
                        	<div id="kategorie-container">
                            	<?php $menu_class = 'category-nav'; //standardowa (niemobilna klasa dla menu kategorii)
								if ( !wp_is_mobile() ) {
								//Dla standardowej - niemobilnej przeglądarki
									
									echo '<p>Wyświetlane wydarzenia: '.$nazwa_kategorii.'  </p>'; 
								}//if ( !wp_is_mobile() )
								else {
								//Dla przeglądarki mobilnej
									$menu_class = 'category-nav-mobile'; //mobilna klasa dla menu kategorii
								}//else - if ( wp_is_mobile() )
                                //Dodatkowe menu zmiany kategorii wydarzenia
                                wp_nav_menu( array(
                                        'theme_location' => 'kursy-category-navigation',
                                        'container' => 'nav',
                                        'menu_class' => $menu_class)
                                ); ?>
                                &nbsp;
                            </div><!--#kategorie-container-->
                        </div><!--#kategorie-wrap-->
					<?php
			}//(!is_single())
?>


<div id="main-wrap">
	
	<div id="main-container" class="clearfix">
    
    	<section id="content-container" class="column-9">
		<?php
			if (!is_single()){
			//Na wszelki wypadek sprawdza czy to jest lista wydarzeń
				
				
				//pobranie treści "przypinanej" nad tabelami kursów, ustawianej w pods ustawienia-edukacji
				//echo $my_option = get_option( 'ustawienia_edukacji_tresc' ); // my_custom_settings is the pod, my_option is the field
				$paramsSet = array( 	'limit' => -1);
				$podsSet = pods( 'ustawienia_edukacji', $paramsSet );
				if(!empty($podsSet)){
					echo $tresc = $podsSet->display('tresc');
					
				}//if ($pods->exists())

                //get pods object
				//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
				$pods = pods( 'kursy', $params );
                //loop through records
                if ( $pods->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
					
					//rozpoczęcie tabeli
					?>
                    <table id="tabelaKursow" class="column-9" frame="void" cellspacing="0" cols="4" rules="none">
						<tbody>
					<?php
					
					if(($nazwa_kategorii=='wszystkie')||(!in_array($slug,$zdefiniowane_kategorie_glowne))){
					//Sprawdzenie czy należy grupować kursy (według kategorii kursów oznaczonych jako główne)
					//Grupowanie ma miejsce gdy wybrano do wyświetlenia wszystkie kursy lub jakąś kategorię (np. dla dorosłych) nie będącą kategorią główną.
					//Zatem występuje gdy $nazwa_kategorii=='wszystkie' lub $slug wybranej kategorii kursów nie znajduje się w tablicy $zdefiniowane_kategorie_glowne[
						
						foreach ($zdefiniowane_kategorie_glowne as $kategoria_glowna){
						//pętla po wszystkich kategoriach głównych
						
							
							$params = array( 	'limit' => -1,
												'where'   => 'kategorie_kursu.slug LIKE "%'.$kategoria_glowna.'%"');
												
							$pods_kursy_kategorii_glownej = pods( 'kursy', $params );
							if ( $pods_kursy_kategorii_glownej->total() > 0 ) {
								
								//zmienna określająca, czy już wyświetlono wiersz(nagłówek) kategorii głównej
								$wyswietlono_wiersz_kategorii_glownej = false;
								
								//pętla po wszystkich znalezionych rekordach
								while ( $pods_kursy_kategorii_glownej->fetch() ) {
									$kategorie_slug = $pods_kursy_kategorii_glownej->field('kategorie_kursu.slug');
									if((in_array($slug, $kategorie_slug))||($nazwa_kategorii=='wszystkie')){
									//wyświetlenie wiersza - kursu tylko jeśli wybrano kategorię wszystkie lub kurs należy do wybranej kategorii
									//czyli wybrany $slug znajduje się w tabeli $kategorie_slug tego kursu
										//Rysowanie wiersza nagłówka, jeśli jeszcze nie zostało to zrobione:

										$wyswietlono_wiersz_kategorii_glownej = rysujWierszNaglowkaKategoriiGlownej($wyswietlono_wiersz_kategorii_glownej, $kategoria_glowna, $zdefiniowane_kategorie_kursow,  $zdefiniowane_kategorie_kursow_nazwy);

										//Wyświetlenie wiersza w tabeli
										$kolor_tabeli_kursu = rysujWierszTabeliKursow($pods_kursy_kategorii_glownej, $kolor_tabeli_kursu);
									}//((in_array($slug, $kategorie_slug))||($nazwa_kategorii=='wszystkie'))
								}//while ( $pods_kursy_kategorii_glownej->fetch() )
							}//( $pods_kursy_kategorii_glownej->total() > 0 )
											
						}//foreach ($zdefiniowane_kategorie_glowne as $kategoria_glowna)
						
					}//if(($nazwa_kategorii=='wszystkie')||(!in_array($slug,$zdefiniowane_kategorie_glowne)))
					else{
					//Bez grupowania
						//wiersz nagłówka kategorii
						rysujWierszNaglowkaKategoriiGlownej(false, $slug, $zdefiniowane_kategorie_kursow,  $zdefiniowane_kategorie_kursow_nazwy);
						while ( $pods->fetch() ) {
						//pętla po wszystkich znalezionych kursach
						//generuje wiersze tabeli dla wszystkich znalezionych kursów z wybranej kategorii:
							//echo $kolor_tabeli_kursu;
							
							$kolor_tabeli_kursu = rysujWierszTabeliKursow($pods, $kolor_tabeli_kursu);
							
						}//while ( $pods->fetch() )
					}//else if(($nazwa_kategorii=='wszystkie')||(!in_array($slug,$zdefiniowane_kategorie_glowne)))
					
					
                    
					//zakończenie tabeli
					?>
                    		</tbody>
                        </table>
					<?php
				}//if ( $pods->total() > 0 )
				else{
				//jeśli nie znaleziono kursów spełniających określone kryteria
					?>
                    <h3>Kursy i sekcje 2015</h3>
<p>Zapraszamy wszystkich, którzy uważają, że nie mają zdolności artystycznych, a chcieliby być "częścią" tworzonego dzieła; osoby, które są uzdolnione i pragną się dalej rozwijać oraz chcą się dowiedzieć jakie techniki zastosować, aby stworzyć wyjątkowe dzieło sztuki.</p>

<p>Nasze zajęcia sprawią, iż każdy uwierzy, że może być twórcą.</p>

<p>Tegoroczne warsztaty zostały zaplanowane z myślą o dzieciach i osobach dorosłych. Uczestniczyć w nich może każdy, kto ma chęć zapoznania się z różnymi technikami pracy artystycznej.</p>

<p>Zajęcia będą podzielone na trzy grupy wiekowe (6-8 lat, 9-12 lat oraz młodzież i dorośli)- minimalna frekwencja danej grupy to 8 osób. Warsztaty będą się składać z pięciu działów tematycznych: grafika artystyczna, rysunek (ołówek, kredki), malarstwo, decuapage i quilling (papieroplastyka). Poprowadzi je pięciu instruktorów, wykwalifikowanych w danej dziedzinie sztuki.</p>

<p>Jest to oferta, jakiej jeszcze w Centrum Sztuki nie było, a która będzie rozwijana przez kolejne lata. Dzieła uczestników zajęć będą eksponowane regularnie w Galeriach Ośrodka Kultury.</p>

<p>Zapraszamy do zapisów (telefonicznie 71 313 28 29, wew. 20 lub mailowo – kursy@kultura.olawa.pl) , które prowadzi Ośrodek Kultury przez cały miesiąc wrzesień.</p>

<p>FOTOGRAFIA:
Spotkanie organizacyjne: 22.09 o godz. 18:00 Ośrodek Kultury ul. 11 Listopada 27/sala wykładowa</p>

<p>ZABAWY Z PIOSENKĄ:
Spotkanie organizacyjne: 22.09 o godz. 16:30 Ośrodek Kultury ul. 11 Listopada 27/ sala nr 18</p>

<p>Kursy i sekcje otwieramy przy minimum 8 osobowej frekwencji.
Serdecznie zapraszamy!</p>
                    
                    <!-- WERSJA STANDARDOWA
                    <h2>Brak zajęć edukacyjnych z kategorii <?php //echo $nazwa_kategorii ?>.</h2>
                    <p>Aktualnie Centrum Sztuki w Oławie nie prowadzi żadnych zajęć edukacyjnych z kategorii - <?php //echo $nazwa_kategorii ?>.</p>
					<p>Zapraszamy do zapoznania się ofertą zajęć edukacyjnych z innych kategorii:<br />-->
                    
                    <?php
						
						echo '<a href="'.home_url().'/kategorie-edukacji/wszystkie-zajecia/">wszystkie kategorie</a> ';
						
						//http://www.kulturaolawa.nazwa.pl/testy_laboratorium/kategorie-edukacji/wszystkie-zajecia/
						
						//wypisanie odnośników do wszystkich innych kategorii poza aktualnie otwartą (w której nic nie znaleziono)
						for($i = 0; $i < count($zdefiniowane_kategorie_kursow_nazwy); $i++)
						{
							if($zdefiniowane_kategorie_kursow[$i] != $nazwa_kategorii)
							//jeśli dana kategoria nie jest kategorią dla której właśnie nie znaleziono żadnych kursów to wyświetlany odnośnik do niej
							{
								echo '<a href="'.home_url().'/kategorie_edukacji/'.$zdefiniowane_kategorie_kursow[$i].'/">'.$zdefiniowane_kategorie_kursow_nazwy[$i].'</a> ';
							}
						}for($i = 0; $i < count($zdefiniowane_kategorie_kursow_nazwy); $i++)
					?>
                    </p>
					
					<?php
				}//else od if ( $pods->total() > 0 )
			}//if (!is_single())
			else{
				//Jeśli nie jest to lista kursów zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
				?>
                	<p>Błąd listy kursów szablonu EDUKACJA. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od if (!is_single())
		?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>