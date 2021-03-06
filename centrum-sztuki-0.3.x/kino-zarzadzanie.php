﻿<?php
/*
Template Name: Kino - Zarządzanie
Description: Obsługuje stronę listy projekcji filmowych stylu Centrum Sztuki w Oławie korzystając z pods filmy i projekcje. Jest to widok roboczy.
Tylko dla użytkowników zalogowanych (posiadających uprawnienia do publikacji postów). 

*/

get_header(); ?>


	

<div id="main-wrap">
	<div id="main-container" class="clearfix">

    	<section id="content-container" class="column-9">
		<?php
			if (current_user_can( UPR_KINO_ZARZADZANIE )){
			//jeśli jest zalogowany użytkownik o uprawnieniach zgodnych z wymaganymi do dostępu do tej strony
				
				?>
                <h1>Projekcje filmów</h1>
                <a class="nowy-termin" target="_blank" href="<?php echo home_url().'/dodaj-projekcje/' ?>">Dodaj nowe projekcje</a> <?php
				
				function wyswietlajRepertuarDniami($ilosc_dni, $dzien_poczatku = NULL){
					//Funkcja wyświetlająca repertuar kina na zadaną ilość dni
					//Jeśli $dzien_poczatku zdefiniowany, to zaczyna od niego, jeśli nie, to od dnia bieżącego
	
					
					if(is_null($dzien_poczatku) || !walidujDate($dzien_poczatku)){
					//Jeśli parametr $dzien_poczatku jest NULL lub nie zawiera prawidłowej daty w formacie "Y-m-d" czyli YYYY-MM-DD
					//to przypisywana jest mu data dzisiejsza
					//korzysta z funkcji walidujDatę z functions.php
						$dzien_poczatku = date("Y-m-d");
					}
					
					for($i=0; $i < $ilosc_dni; $i++){
		
						$datetime = new DateTime($dzien_poczatku);
						$datetime->modify('+'.$i.' day');
						$dzien_szukany = $datetime->format('Y-m-d');
											
						$params = array( 	'limit' => -1,
											'where' => 'DATE( termin_projekcji.meta_value ) = "'.$dzien_szukany.'" 
														AND (t.post_status = "draft" OR t.post_status = "publish" OR t.post_status = "pending" OR t.post_status = "future")',
											'orderby'  => 'termin_projekcji.meta_value');
						
						//get pods object
						//'where' => 't.post_status LIKE "publish"',
						//'where' => 'DATE( termin_projekcji.meta_value ) = "'.$dzien_szukany.'"',
		
						$pods = pods( 'projekcje', $params );
						//loop through records
						if ( $pods->total() > 0 ) {
							//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
							
							if(pobieczCzescDaty('w',$dzien_szukany) == 3){
								//jeśli wyświetlany w repertuarze dzień tygodnia to środa
								$dzien_wyswietlany = 'TANIA ŚRODA';
							}
							else{
								$dzien_wyswietlany = zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_szukany), FALSE);
							}
							
							echo '<h1>'.$dzien_wyswietlany.', '.zamienDateGodzinePodsNaTekst($dzien_szukany).'</h1>';
							
							while ( $pods->fetch() ) {
								//Put field values into variables
								//$title = $pods->display('name');
								$permalink = $pods->field('permalink' );
								$tytul_filmu =  $pods->display('film');
								
								$post_status = $pods->field('post_status');
								$permalink_filmu  =  $pods->field('film.permalink');
								$picture = $pods->field('film.obraz');
								$tytul_oryginalny =  $pods->display('film.tytul_oryginalny');
								$termin_projekcji = $pods->field('termin_projekcji' );
								$q2d3d = $pods->field('2d3d');
								$projekcja_wersja_jezykowa = $pods->display('wersja_jezykowa');
								$standardowa_wersja_jezykowa = $pods->display('film.standardowa_wersja_jezykowa');
								$kategoria_wiekowa = $pods->display('film.kategoria_wiekowa');
								$czas_trwania = $pods->display('film.czas_trwania');
								$kraj_produkcji = $pods->display('film.kraj_produkcji');
								$rok_produkcji = $pods->display('film.rok_produkcji');
								$gatunek_filmowy =  $pods->field('film.gatunek_filmowy.name');
								$home_url = get_home_url();
								
								$inny_komunikat_o_biletach = $pods->display('inny_komunikat_o_biletach');
								$opcje_sprzedazy = $pods->field('opcje_sprzedazy');
								$id_w_sprzedazy_online = $pods->field('id_w_sprzedazy_online');
								$dzien_publikacji_odnosnika_do_biletow = $pods->field('dzien_publikacji_odnosnika_do_biletow');
								$godzina_publikacji_odnosnika_do_biletow = $pods->field('godzina_publikacji_odnosnika_do_biletow');
								if(!empty($dzien_publikacji_odnosnika_do_biletow) && $godzina_publikacji_odnosnika_do_biletow==0){
									//jeśli wybrano dzień publikacji odnośnika, a nie wybrano godziny (wybrano 00:00) to wstawiana jest standardowa 10:00
									$godzina_publikacji_odnosnika_do_biletow = '10:00';
								}
								
								$id = $pods->field('ID');
								$post_status = get_post_status( $id ); //pobiera status posta (np. opublikowany, szkic, oczekujący)
								
								switch ($post_status) {
														case "publish":
															$post_status = "Opublikowane";
															$klasa_tabeli = "";
															break;
														case "draft":
															$post_status = "Szkic";
															$klasa_tabeli = "kino-zarzadzanie-nieopublikowane";
															break;
														case "pending":
															$post_status = "Oczekuje na przegląd";
															$klasa_tabeli = "kino-zarzadzanie-nieopublikowane";
															break;
														case "future":
															$post_status = "Zaplanowana publikacja";
															$klasa_tabeli = "kino-zarzadzanie-nieopublikowane";
															break;
														default:
															$post_status = NULL;
								}//switch ($post_status)
								
								
					?>			<table class="kino-zarzadzanie <?php echo $klasa_tabeli ?>">
                    			
                    			<tr>
                                
                                
                                <td colspan="2" class="status"><?php echo $post_status ?></td>
                                    <td rowspan="4" class="bottom img"><?php 
												echo '<a href="'.$permalink_filmu.'">';
													//jeśli film ma przypisaną miniaturę, to jest ona wyświetlana
													if (( !is_null($picture) )&&(!empty($picture))){
														echo wp_get_attachment_image( $picture['ID'], 'projekcja-thumb' ); 
													}
													//jeśli wydarzenie nie ma przypisanej miniatury to jest wyświetlany standardowy obrazek pegaz_thumb.jpg
													else{
														echo '<img src="'.get_stylesheet_directory_uri().'/pegaz_kino_thumb.png" />';	
													}
												echo '</a>';
												?>
                                                
                                    </td> 
                                </tr>
                                <tr>
                                <th class="width30proc"><?php echo pobieczCzescDaty('G',$termin_projekcji).':'.pobieczCzescDaty('i',$termin_projekcji) ?></th>
                                <th>
                                	
											 <?php echo $tytul_filmu; 
                                                    if($q2d3d){ echo ' 3D';	} //jeśli projekcja jest 3d (czyli TRUE) dodaje taki dopisek do tytułu
                                                    if(!empty($projekcja_wersja_jezykowa)){ 
                                                    //jeśli wybrano wersję językową dla projekcji to jest ona wyświetlana w tytule
                                                        echo " /$projekcja_wersja_jezykowa"; 
                                                    }
                                                    else if(!empty($standardowa_wersja_jezykowa)){
                                                        //jeśli wybrano wersję językową dla filmu (i nie jest ona nadpisana przez wersję projekcji
                                                        //to jest ona wyświetlana w tytule
                                                        echo " /$standardowa_wersja_jezykowa"; 
                                                    }
                                            ?>
                                          <a href="<?php echo home_url().'/wp-admin/post.php?post='.$id.'&action=edit' ?>" target="_blank">Edytuj projekcję</a>
                                          
                                          
                                </th>
                                 
                                
                                </tr>
                                <tr>
                                <td><?php if($q2d3d){ echo ' 3D';} else {echo '2D';} ?></td>
                                <td><?php if(!empty($projekcja_wersja_jezykowa)){ 
											//jeśli wybrano wersję językową dla projekcji
												echo "$projekcja_wersja_jezykowa (Z ust. terminu)"; 
											}
											else if(!empty($standardowa_wersja_jezykowa)){
												//jeśli wybrano wersję językową dla filmu (i nie jest ona nadpisana przez wersję projekcji
												//to jest ona wyświetlana w tytule
												echo "$standardowa_wersja_jezykowa (Z ust. filmu)"; 
											} ?></td>
                                </tr>
                                <tr>
                                	<?php if(!empty($inny_komunikat_o_biletach)){
												//jeśli pole inny_komunikat_o_biletach nie jest pusty to wyświetlana jest jego treść a cała reszta pomijana
												echo '<td colspan="2">'.$inny_komunikat_o_biletach.'</td>';
												}//if(!empty($inny_komunikat_o_biletach))
												else{
													switch ($opcje_sprzedazy) {
														case "wstep_wolny":
															echo '<td colspan="2">Wstęp wolny</td>';
															break;
														case "brak_biletow":
															echo '<td colspan="2">Brak biletów</td>';
															break;
														case "tylko_kasa":
															echo '<td colspan="2">Bilety tylko w kasie</td>';
															break;
														default:
															$termin_publikacji_odnosnika = new DateTime($dzien_publikacji_odnosnika_do_biletow.' '.$godzina_publikacji_odnosnika_do_biletow);
															//$termin_publikacji_odnosnika  = new DateTime();
															$teraz = pobierzDateTeraz();
														   if(empty($dzien_publikacji_odnosnika_do_biletow) || $termin_publikacji_odnosnika < $teraz){
															   //jeśli minął już termin publikacji odnośnika lub nie wybrano dnia publikacji (równoznaczne													
															   echo '<td>Odnośnik opublikowany</td>';
															   //z opublikowaniem
															   if($id_w_sprzedazy_online > 0){
																//jeśli podany jest id w sprzedaży online
																	//echo '<span class="kup-bilet"><a href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'">Kup bilety</a></span>';
																	echo '<td>ID sprzedaży online '.$id_w_sprzedazy_online.'<span class="kup-bilet"> <a href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'" target="_blank">Sprawdź odnośnik</a></span>';
																	//Odnośnik do zarządzania terminem w VisualTicket
																	echo '<br><a href="http://www.systembiletowy.pl/cso/admin.php/sbSetupRepertoire?repertoire='.$id_w_sprzedazy_online.'" target="_blank">Zarządzanie w VisualTicket</a></td>';
															   }
															   else{
																   //jeśli nie jest podany id w sprzedaży online
																   //echo '<span class="kup-bilet"><a href="'.home_url().'/bilety-online/">Kup bilety</a></span>';													
																   echo '<td><span class="red">Brak ID sprzedaży online</span> - Odnośnik do całego repertuaru online</td>';
															   }
														   }
														   else{
															   //jeśli nie minął jeszcze termin publikacji odnośnika
															   
															   //użycue funkcji zamienDateGodzinePodsNaTekst - chcąc ustawić trzeci parametr (bez wyświetlania roku) na TRUE muszę ustawić drugi na NULL
																echo "<td>Publikacja odnośnika <span class=\"red\">".zamienDateGodzinePodsNaTekst($termin_publikacji_odnosnika->format('Y-m-d'), NULL, TRUE).' - '.$termin_publikacji_odnosnika->format('G:i')."</span></td>";
																
																
																//-------------------
																if($id_w_sprzedazy_online > 0){
																//jeśli podany jest id w sprzedaży online

																	echo '<td>ID sprzedaży online '.$id_w_sprzedazy_online.'<span class="kup-bilet"> <a href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'" target="_blank">Sprawdź odnośnik</a></span><br>';
																	//Odnośnik do zarządzania terminem w VisualTicket
																	echo '<a href="http://www.systembiletowy.pl/cso/admin.php/sbSetupRepertoire?repertoire='.$id_w_sprzedazy_online.'" target="_blank">Zarządzanie w VisualTicket</a>
																	</td>';
															   }//if($id_w_sprzedazy_online > 0)
															   else{
																   //jeśli nie jest podany id w sprzedaży online
																   //echo '<span class="kup-bilet"><a href="'.home_url().'/bilety-online/">Kup bilety</a></span>';													
																   echo '<td><span class="red">Brak ID sprzedaży online</span> - Odnośnik do całego repertuaru online</td>';
															   }//else if($id_w_sprzedazy_online > 0)
															}
													}
													
												}//else od if(!empty($inny_komunikat_o_biletach))
										?>
                                      
                                        
                                </tr>
                    			</table><!--kino-zarzadzanie-->
                                <?php if(0){ //testowa - można ręcznie włączyć lub wyłączyć wyświetlanie ?>
								<article class="projekcja column-12">
									<div class="projekcja-pasek">
										<div class="projekcja-termin">
											<p class="projekcja-godzina">
														<?php echo pobieczCzescDaty('G',$termin_projekcji) ?>
														<span class="minuty">
															<?php echo pobieczCzescDaty('i',$termin_projekcji) ?>
														</span>
											</p>
											<p class="projekcja-dzien">
												<?php echo pobieczCzescDaty('j',$termin_projekcji).' '.ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$termin_projekcji)) ?>
											</p>
										</div><!--.termin-->
										<div class="projekcja-thumb">
											<?php 
											//jeśli film ma przypisaną miniaturę, to jest ona wyświetlana
											if (( !is_null($picture) )&&(!empty($picture))){
												echo wp_get_attachment_image( $picture['ID'], 'projekcja-thumb' ); 
											}
											//jeśli wydarzenie nie ma przypisanej miniatury to jest wyświetlany standardowy obrazek pegaz_thumb.jpg
											else{
												echo '<img src="'.get_stylesheet_directory_uri().'/pegaz_kino_thumb.png" />';	
											}?>
										</div><!--termin-thumb-->
										
										<div class="projekcja-tresc">
											<h1 class="projekcja-tytul">
												<a href="<?php echo $permalink_filmu ?>">
													 <?php echo $tytul_filmu; 
															if($q2d3d){ echo ' 3D';	} //jeśli projekcja jest 3d (czyli TRUE) dodaje taki dopisek do tytułu
															if(!empty($projekcja_wersja_jezykowa)){ 
															//jeśli wybrano wersję językową dla projekcji to jest ona wyświetlana w tytule
																echo " /$projekcja_wersja_jezykowa"; 
															}
															else if(!empty($standardowa_wersja_jezykowa)){
																//jeśli wybrano wersję językową dla filmu (i nie jest ona nadpisana przez wersję projekcji
																//to jest ona wyświetlana w tytule
																echo " /$standardowa_wersja_jezykowa"; 
															}
													?>
												  </a>
											</h1>
											
											<div class="projekcja-opis">
												<p>
												<?php /*echo $termin_projekcji*/ ?>
												
												<?php
														/*TYTUŁ ORYGINALNY*/
														if(!empty($tytul_oryginalny)){
															//Jeśli podano czas trwania
															echo "Tytuł oryg.: $tytul_oryginalny<br>";
														}
												
														/*KATEGORIA WIEKOWA*/
														if($kategoria_wiekowa > 0){
														//jeśli podano jakąś kategorię wiekową to ją wyświetla
															echo "Od lat: $kategoria_wiekowa,";
														}
														else
														//jeśli nie podano kategorii
														{
															echo "Od lat: b.o.,";
														}
														
														/*CZAS TRWANIA*/
														if(!empty($czas_trwania)){
															//Jeśli podano czas trwania
															echo " Czas trwania: $czas_trwania min<br>";
														}
														
														/*KRAJ I ROK PRODUKCJI*/
														if(!empty($kraj_produkcji) || !empty($rok_produkcji)){
															echo "Produkcja: $kraj_produkcji [$rok_produkcji]<br>";
														}
														
														/*GATUNEK FILMOWY*/
														if (!empty($gatunek_filmowy)) {
															echo "Gatunek: ";
															if(is_array($gatunek_filmowy)){
																foreach($gatunek_filmowy as $gat){
																	echo "$gat ";
																}//foreach($gatunek_filmowy as $gat)
															}//if(is_array($gatunek_filmowy
															else
																echo $gatunek_filmowy;
														}//if (!empty($gatunek_filmowy ))
														
														echo $post_status;
												?>
												</p>
											</div><!--.projekcja-opis-->
										  </div><!--.projekcja-tresc-->
										
										<a class="projekcja-czytajWiecej" href="<?php echo esc_url( $permalink_filmu); ?>"><div>Czytaj<br />
		więcej</div></a>
									</div><!--.projekcja-pasek-->
									
									<!-- KUP BILET, TYLKO W KASIE, BRAK BILETÓW itp. -->
									<div class="projekcja-bilety">
										<?php if(!empty($inny_komunikat_o_biletach)){
												//jeśli pole inny_komunikat_o_biletach nie jest pusty to wyświetlana jest jego treść a cała reszta pomijana
												echo '<span>'.$inny_komunikat_o_biletach.'</span>';
												}//if(!empty($inny_komunikat_o_biletach))
												else{
													switch ($opcje_sprzedazy) {
														case "wstep_wolny":
															echo '<span>Wstęp wolny</span>';
															break;
														case "brak_biletow":
															echo '<span class="brak-biletow">Brak biletów</span>';
															break;
														case "tylko_kasa":
															echo '<span class="bilety-kasa"><a href="'.home_url().'/o-nas/kasa/">Bilety do nabycia w kasie</a></span>';
															break;
														default:
															$termin_publikacji_odnosnika = new DateTime($dzien_publikacji_odnosnika_do_biletow.' '.$godzina_publikacji_odnosnika_do_biletow);
															
															$teraz = pobierzDateTeraz();
														   if(empty($dzien_publikacji_odnosnika_do_biletow) || $termin_publikacji_odnosnika < $teraz){
															   //jeśli minął już termin publikacji odnośnika lub nie wybrano dnia publikacji (równoznaczne
															   //z opublikowaniem
															   if($id_w_sprzedazy_online > 0){
																	echo '<span class="kup-bilet"><a href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'">Kup bilety</a></span>';
															   }
															   else{
																   echo '<span class="kup-bilet"><a href="'.home_url().'/bilety-online/">Kup bilety</a></span>';
															   }
														   }
														   else{
															   
															   //użycue funkcji zamienDateGodzinePodsNaTekst - chcąc ustawić trzeci parametr (bez wyświetlania roku) na TRUE muszę ustawić drugi na NULL
																echo '<span>Bilety w sprzedaży od '.zamienDateGodzinePodsNaTekst($termin_publikacji_odnosnika->format('Y-m-d'), NULL, TRUE).'</span>';
																													   }
													}
													
												}//else od if(!empty($inny_komunikat_o_biletach))
										?>
									</div><!--.projekcja-bilety-->
									
								</article>
                                <?php } //if(0)?>
					<?php
							}//while ( $pods->fetch() )
						}//if ( $pods->total() > 0 )
						else{
						//jeśli nie znaleziono żadnej projekcji w danym dniu
							//
						}//else od if ( $pods->total() > 0 )
					}//for(int $i=0; $i < $ilosc_dni; $i++)//
					
					?><a class="nowy-termin" target="_blank" href="<?php echo home_url().'/dodaj-projekcje/' ?>">Dodaj nowe projekcje</a> <?php
				}//wyswietlajRepertuarDniami
			
				if (!is_single()){
	
					wyswietlajRepertuarDniami(90);
					
				}//if (!is_single())
				else{
					//Jeśli nie jest to lista wydarzeń zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
					?>
						<p>Błąd listy repertuaru szablonu KINO kino.php. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
					<?php
				}//else od if (!is_single())
				
				/* CZĘŚĆ SERWISOWA - wyświetlana pod tabelami zarządzania filmami */
				$teraz = pobierzDateTeraz();
				echo 'Aktualna data i czas serwera: '.$teraz->format('Y-m-d H:i'); /*TEST*/
				

								
				?>
                <ul id="repertuar">
                </ul>
                
                <script> 
					processXML(); 
					//processJSON();
                </script>
				<?php

				
			}//if (current_user_can( UPR_KINO_ZARZADZANIE ))
			else{
				echo "<h1>Przepraszamy, nie masz uprawnień do tej strony.</h1>";
			}//else - if (current_user_can( UPR_KINO_ZARZADZANIE ))?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>