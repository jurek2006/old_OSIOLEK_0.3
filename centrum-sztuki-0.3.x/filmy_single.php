<?php
/*
Template Name: Filmy Single
Description: Obsługuje strony pojedynczych filmów stylu Centrum Sztuki w Oławie korzystając z pods filmy oraz pods projekcje 
Listy filmów (repertuar) obsługiwane są przez szablon projekcje.php
*/

get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container">
		<?php
			if (is_single()){
			//Na wszelki wypadek sprawdza, czy jest to pojedyncza strona wydarzenia
				
				//pobranie slug aktulnie otwartego wydarzenia i wczytanie dla niego pods'a
				$slug = pods_v('last','url');
				//get pods object
				$pods = pods( 'filmy', $slug );
				
				if ($pods->exists()){
				
				//Put field values into variables
				$title = $pods->display('name');
				
				$picture = $pods->field('obraz');
				$tytul_oryginalny =  $pods->display('tytul_oryginalny');
				$kategoria_wiekowa = $pods->display('kategoria_wiekowa');
				$czas_trwania = $pods->display('czas_trwania');
				$kraj_produkcji = $pods->display('kraj_produkcji');
				$rezyseria = $pods->display('rezyseria');
				$obsada = $pods->display('obsada');
				$rok_produkcji = $pods->field('rok_produkcji');
				$gatunek_filmowy =  $pods->field('gatunek_filmowy.name');
				
				$post_content = $pods-> display('post_content');
				?>
				
                            
				<div class="film-single">
                	<article class="film-single column-9">
                        <div class="film-pasek clearfix">
                        <?php
                            //jeśli film ma przypisaną miniaturę, to jest ona wyświetlana
                                            if (( !is_null($picture) )&&(!empty($picture))){
												$atr = wp_get_attachment_image_src( $picture['ID'], 'full' );
												
												echo "<a href=\"$atr[0]\" title=\"Zobacz plakat\">";
                                                echo wp_get_attachment_image( $picture['ID'], 'medium' ); 
												echo "</a>";
                                            }
                                            //jeśli wydarzenie nie ma przypisanej miniatury to jest wyświetlany standardowy obrazek pegaz_thumb.jpg
                                            else{
                                                echo '<img src="'.get_stylesheet_directory_uri().'/pegaz_kino_thumb.png" />';	
                                            }
                        ?>
                        <div class="film-info">
                        	<h1 class="film-single-tytul">
								 <?php echo _e( $title , 'PP2014' ); ?>
                            </h1>
                            
                           <?php /*TYTUŁ ORYGINALNY*/
								if(!empty($tytul_oryginalny)){
									//Jeśli podano czas trwania
									echo '<p class="tytul-oryginalu">'."$tytul_oryginalny<br><p>";
								}
								
								/*ZAPOWIEDŹ*/
								$zapowiedz =  $pods->field('zapowiedz');
								$zapowiedz_data_tekstowa = zamienDateGodzinePodsNaTekst($zapowiedz);
								$zapowiedz = new DateTime($zapowiedz);
								if($zapowiedz > pobierzDateTeraz()){
								//sprawdzenie czy nie nastąpił jeszcze dzień, kiedy film przestaje być zapowiedzią
								//jeśli nie, to dodawane są odpowiednie informacje od kiedy będzie on w Kinie ODRA
									$newDate = $zapowiedz->format('d-m-Y'); // 
									$newDateT = zamienDateGodzinePodsNaTekst($zapowiedz);
									
									$niestandardowa_zapowiedz =  $pods->field('niestandardowa_zapowiedz');
									if(empty($niestandardowa_zapowiedz)){
									//jeśli nie wypełniono pola niestandardowa_zapowiedz to wpisywana jest treść (z wybraną datą):
									//Od 5 maja 2015 w Kinie ODRA
										echo "<p class=\"zapowiedz\"><strong>Od $zapowiedz_data_tekstowa w Kinie ODRA</strong></p>";
									}
									else{
										//jeśli wypełniono pole niestandardowa_zapowiedz to wyświetlana jest jego zawartość
										echo "<p class=\"zapowiedz\"><strong>$niestandardowa_zapowiedz</strong></p>";
									}
									
								}
						
								/*KATEGORIA WIEKOWA*/
								if($kategoria_wiekowa > 0){
								//jeśli podano jakąś kategorię wiekową to ją wyświetla
									echo "<p><strong>Od lat:</strong> $kategoria_wiekowa</p>";
								}
								else if($kategoria_wiekowa == 0){
								//jeśli nie wpisano nic lub 0 - wyświetla bez ograniczeń
									echo "<p><strong>Od lat:</strong> b.o.</p>";
								}
								//jeśli podano liczbę ujemną to znaczy, że brak jest danych o kategorii wiekowej i nie ma być to wyświetlane	
								
								/*CZAS TRWANIA*/
								if(!empty($czas_trwania)){
									//Jeśli podano czas trwania
									echo "<p><strong>Czas trwania:</strong> $czas_trwania min</p>";
								}
								
								/*KRAJ I ROK PRODUKCJI*/
								if(!empty($kraj_produkcji) || !empty($rok_produkcji)){
									echo "<p><strong>Produkcja:</strong> $kraj_produkcji [$rok_produkcji]</p>";
								}
								
								/*GATUNEK FILMOWY*/
								if (!empty($gatunek_filmowy)) {
									echo '<p class="z-przerwa">'."<strong>Gatunek:</strong> ";
									if(is_array($gatunek_filmowy)){

										echo implode(", ", $gatunek_filmowy);	
										
									}//if(is_array($gatunek_filmowy
									else{
										echo $gatunek_filmowy;
									}
									echo "</p>";
								}//if (!empty($gatunek_filmowy )) 
								
								/*REŻYSERIA*/
								if (!empty($rezyseria)) {
									echo '<p>'."<strong>Reżyseria:</strong> $rezyseria</p>";
								}
								
								/*OBSADA*/
								if (!empty($obsada)) {
									echo "<p><strong>Obsada:</strong> $obsada</p>";
								}
								?>
                            
                        </div><!--film-info-->
                        </div><!--.film-pasek-->
                        <!--Główna treść opisu filmu-->
                        <?php echo '<p class="opis-filmu">'."<strong>Opis filmu:</strong></p>" ?>
                        <?php echo $post_content; ?>
                    </article><!--.film-single-->
                    </div><!--.film-single-->
				
				<!--====== Pobranie posortowanych wg daty danych o terminach projekcji filmu ========-->
				<div class="film-projekcje column-3">
				<?php
				$projekcje = array_combine($pods->field('projekcje.termin_projekcji'), $pods->field('projekcje'));
				ksort ($projekcje);				
				
                 if ( !empty( $projekcje ) && is_array($projekcje) ) {
							
						$etykietaDnia = NULL;		
						foreach ( $projekcje as $projekcja_termin => $projekcje_dane ) {
							
							
							if(new DateTime($projekcja_termin) >= new DateTime(date("Y-m-d"))){
								
								//fragment odpowiadający za "grupowanie" projekcji wg dnia
								//sprawdza czy etykieta dnia została już wyświetlona (czy to jest ten sam dzień)
								//jeśli nie, to wyświetla etykietę dnia
								$dzienProjekcji = DateTime::createFromFormat("Y-m-d H:i:s", $projekcja_termin);
								$dzienProjekcji = $dzienProjekcji->format('Y-m-d'); 
								
								if($etykietaDnia!=$dzienProjekcji){
									
									if(pobieczCzescDaty('w',$dzienProjekcji) == 3 && pobieczCzescDaty('d-m-Y',$dzienProjekcji)!= '06-01-2016'){
										//jeśli wyświetlany w repertuarze dzień tygodnia to środa
										$dzien_wyswietlany = 'TANIA ŚRODA';
									}
									elseif(pobieczCzescDaty('d-m-Y',$dzienProjekcji) == '06-01-2016'){
										$dzien_wyswietlany = 'Święto Trzech Króli';
									}
									else{
										$dzien_wyswietlany = zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzienProjekcji), FALSE);
									}
									
									echo '<h2>'.$dzien_wyswietlany.', '.zamienDateGodzinePodsNaTekst($dzienProjekcji, '', TRUE).'</h2>';
									
									$etykietaDnia = $dzienProjekcji;
								}
								
								//echo $projekcja_termin;
								//get id for related post and put in id
								$id = $projekcje_dane[ 'ID' ];
								$godzina = DateTime::createFromFormat("Y-m-d H:i:s", $projekcja_termin);
								$godzina = $godzina->format('H:i');
								$czy2d3d = get_post_meta( $id, '2d3d', true );
								//sprawdza czy wybrano wersję 3D (true) czy nie
								if($czy2d3d)
									$czy2d3d = '3D';
								else
									$czy2d3d = '2D';
									
								$projekcja_wersja_jezykowa = get_post_meta( $id, 'wersja_jezykowa', false );
								if(empty($projekcja_wersja_jezykowa[0])){
								//jeśli nie wybrano wersji językowej dla projekcji to pobierana jest ona z ustawień filmu
									$wersja_jezykowa = $pods->display('standardowa_wersja_jezykowa');
								}
								else
								//jeśli pobrano wersję językową projekcji, to jest ona pobierana bezpośrednio z niej
									$wersja_jezykowa = $projekcja_wersja_jezykowa[0];
									
								$id_w_sprzedazy_online = get_post_meta( $id, 'id_w_sprzedazy_online', true );	
								$inny_komunikat_o_biletach = get_post_meta( $id, 'inny_komunikat_o_biletach', true );
								$opcje_sprzedazy = get_post_meta( $id, 'opcje_sprzedazy', true ); //jeśli nie wybrano żadnej opcji to jest empty
								$dzien_publikacji_odnosnika_do_biletow = get_post_meta( $id, 'dzien_publikacji_odnosnika_do_biletow', true );
								$godzina_publikacji_odnosnika_do_biletow = get_post_meta( $id, 'godzina_publikacji_odnosnika_do_biletow', true );
								
								$termin_publikacji_odnosnika = new DateTime($dzien_publikacji_odnosnika_do_biletow.' '.$godzina_publikacji_odnosnika_do_biletow);
								$teraz = pobierzDateTeraz();

									
								
								
								if(!empty($inny_komunikat_o_biletach)){
											//jeśli pole inny_komunikat_o_biletach nie jest pusty to wyświetlana jest jego treść a cała reszta pomijana
											echo "<p><span class=\"nieaktywny-przycisk\">Kup bilety</span> <strong>$godzina</strong> [$czy2d3d/$wersja_jezykowa]<br /><span class=\"komentarz\">$inny_komunikat_o_biletach</span></p> ";
											}//if(!empty($inny_komunikat_o_biletach))
											else{
												switch ($opcje_sprzedazy) {
													case "wstep_wolny":
														echo "<p><span class=\"nieaktywny-przycisk\">Kup bilety</span> <strong>$godzina</strong> [$czy2d3d/$wersja_jezykowa]<br /><span class=\"komentarz\">Wstęp wolny</span></p> ";
														break;
													case "brak_biletow":
														echo "<p><span class=\"nieaktywny-przycisk\">Kup bilety</span> <strong>$godzina</strong> [$czy2d3d/$wersja_jezykowa]<br /><span class=\"komentarz\">Brak biletów</span></p> ";
														break;
													case "tylko_kasa":
														echo "<p><span class=\"nieaktywny-przycisk\">Kup bilety</span> <strong>$godzina</strong> [$czy2d3d/$wersja_jezykowa]<br />".'<a class=\"komentarz\" href="'.home_url().'/o-nas/kasa/">Bilety do nabycia tylko w kasie</a></p> ';
														break;
													default:
														$termin_publikacji_odnosnika = new DateTime($dzien_publikacji_odnosnika_do_biletow.' '.$godzina_publikacji_odnosnika_do_biletow);
														$teraz = new DateTime(date("Y-m-d H:i"));
													   if(empty($dzien_publikacji_odnosnika_do_biletow) || $termin_publikacji_odnosnika < $teraz){
														   //jeśli minął już termin publikacji odnośnika lub nie wybrano dnia publikacji (równoznaczne
														   //z opublikowaniem
														   if($id_w_sprzedazy_online > 0){
														   		echo "<p><a class=\"aktywny-przycisk\" href=\"http://www.systembiletowy.pl/cso/index.php/repertoire.html?id=$id_w_sprzedazy_online\">Kup bilety</a> <strong>$godzina</strong> [$czy2d3d";
																if(!empty($wersja_jezykowa)){
																	echo "/$wersja_jezykowa";
																}
																echo "]</p> ";
														   }
														   else{
															    echo "<p><a class=\"aktywny-przycisk\" href=\"".home_url()."/bilety-online/\">Kup bilety</a> <strong>$godzina</strong> [$czy2d3d";
																if(!empty($wersja_jezykowa)){
																	echo "/$wersja_jezykowa";
																}
																echo "]</p> ";
														   }
													   }
													   else{
														   
														   //użycue funkcji zamienDateGodzinePodsNaTekst - chcąc ustawić trzeci parametr (bez wyświetlania roku) na TRUE muszę ustawić drugi na NULL
															//Bilety w sprzedaży od '.zamienDateGodzinePodsNaTekst($termin_publikacji_odnosnika->format('Y-m-d'), NULL, TRUE)
															
															echo "<p><span class=\"nieaktywny-przycisk\">Kup bilety</span> <strong>$godzina</strong> [$czy2d3d";
															if(!empty($wersja_jezykowa)){
																echo "/$wersja_jezykowa";
															}
															echo "]<br /><span class=\"komentarz\">W sprzedaży od ".zamienDateGodzinePodsNaTekst($termin_publikacji_odnosnika->format('Y-m-d'), NULL, TRUE)."</span></p> ";
																												   }
												}
												
											}//else od if(!empty($inny_komunikat_o_biletach))
								
							}
						}//foreach ( $projekcje as $proj ) {
				 }//if ( !empty( $projekcje ) && is_array($projekcje) )
		 
				 ?>
                 </div><!--.film-projekcje-->
				 <!--====== KONIEC Pobranie posortowanych wg daty danych o terminach projekcji filmu ========-->
				 
				 <?php
				}//if ($pods->exists())
			}//(is_single())
			else{
				//Jeśli nie jest to strona pojedynczego wydarzenia - błąd. Zostaje to wyświetlone.
				?>
                <p>Błąd szablonu pojedynczego filmu FILMY_SINGLE. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od (is_single())
		?>
		</section><!-- #content-container -->
        <?php //get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>