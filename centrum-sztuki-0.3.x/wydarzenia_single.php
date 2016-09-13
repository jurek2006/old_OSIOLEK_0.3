<?php
/*
Template Name: Wydarzenia Single
Description: Obsługuje strony pojedynczych wydarzeń stylu Centrum Sztuki w Oławie korzystając z pods wydarzenia oraz pods kategorie (dla kategorii wydarzeń).
Listy wydarzeń obsługiwane są przez wydarzenia.php
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
				$pods = pods( 'wydarzenia', $slug );
				
				if ($pods->exists()){
				
				//Put field values into variables
				$title = $pods->display('name');
				$picture = $pods->field('lokalizacje.adres.zdjecie_miniatura');
				$picture = pods_image ( $picture, $size = 'full', $default = 0, $force = false );

				
				//pobieranie koloru tła na podstawie lokalizacji
				$kolor_tla_naPodstLokalizacji = $pods->field('lokalizacje.adres.kolor');
				$kolor_tla_naPodstLokalizacji = $kolor_tla_naPodstLokalizacji[0];
				
				$termin_opisowy = $pods->display('termin_opisowy');
				$lokalizacje = $pods->field('lokalizacje.name');
				$lokalizacje = $lokalizacje[0];
				
				$lokalizacje_krotka_nazwa = $pods->field('lokalizacje.krotka_nazwa');
				//jeśli nie zdefiniowano krótkiej nazwy dla lokalizacji używana jest nazwa pełna
				if(empty($lokalizacje_krotka_nazwa)){
					$lokalizacje_krotka_nazwa = $lokalizacje;
				}
				else{
					$lokalizacje_krotka_nazwa = $lokalizacje_krotka_nazwa[0];
				}
				
				$lokalizacje_adres_nazwa = $pods->field('lokalizacje.adres.name');
				$lokalizacje_adres_nazwa = $lokalizacje_adres_nazwa[0];
				
				$lokalizacje_adres_adres = $pods->field('lokalizacje.adres.adres');
				$lokalizacje_adres_adres = $lokalizacje_adres_adres[0];
				
				$lokalizacje_adres_miejscowosc = $pods->field('lokalizacje.adres.miejscowosc');
				$lokalizacje_adres_miejscowosc = $lokalizacje_adres_miejscowosc[0];
				
				$lokalizacje_adres_kod_pocztowy = $pods->field('lokalizacje.adres.kod_pocztowy');
				$lokalizacje_adres_kod_pocztowy = $lokalizacje_adres_kod_pocztowy[0];
				
				$lokalizacje_adres_slug = $pods->field('lokalizacje.adres.slug');
				$lokalizacje_adres_slug = $lokalizacje_adres_slug[0];
				

				$data_i_godzina_wydarzenia = $pods->display('data_i_godzina_wydarzenia');
				$dzien_rozpoczecia = $pods->display('dzien_rozpoczecia');
				$dzien_zakonczenia = $pods->display('dzien_zakonczenia');
				$termin_opisowy = $pods->display('termin_opisowy');
				
				$inny_komunikat_o_biletach = $pods->display('inny_komunikat_o_biletach');
				$opcje_sprzedazy = $pods->field('opcje_sprzedazy');
				$id_w_sprzedazy_online = $pods->field('id_w_sprzedazy_online');
				$dzien_publikacji_odnosnika_do_biletow = $pods->field('dzien_publikacji_odnosnika_do_biletow');
				$godzina_publikacji_odnosnika_do_biletow = $pods->field('godzina_publikacji_odnosnika_do_biletow');
				if(!empty($dzien_publikacji_odnosnika_do_biletow) && $godzina_publikacji_odnosnika_do_biletow==0){
					//jeśli wybrano dzień publikacji odnośnika, a nie wybrano godziny (wybrano 00:00) to wstawiana jest standardowa 10:00
					$godzina_publikacji_odnosnika_do_biletow = '10:00';
				}
				
				$cena_biletu_opisowa = $pods->display('cena_biletu_opisowa');
				$cena_biletu = $pods->display('cena_biletu');
				
				$post_content = $pods-> display('post_content');

				// SPRAWDZENIE, CZY WYDARZENIE NIE JEST WYDARZENIEM ARCHIWALNYM
				// Wydarzenie jest archiwalne jeśli (data_i_godzina_wydarzenia.meta_value < NOW()) AND (dzien_zakonczenia.meta_value < NOW()) czyli minęła już data i godzina wydarzenia oraz data zakończenia
				// Jeśli wydarzenie jest archiwalne ustawia $wydarzenieArchiwalne = true

				$wydarzenieArchiwalne = false;
				$dzisiaj = new DateTime(date("Y-m-d"));

				$n_data_i_godzina_wydarzenia = DateTime::createFromFormat("Y-m-d H:i", $data_i_godzina_wydarzenia);
				$n_dzien_zakonczenia = DateTime::createFromFormat("Y-m-d", $dzien_zakonczenia);

				if(($n_data_i_godzina_wydarzenia < $dzisiaj) && /*($n_dzien_zakonczenia !=false) &&*/ ($n_dzien_zakonczenia < $dzisiaj)){
					$wydarzenieArchiwalne = true;
				}

				?>
                
                <h1 class="wydarzenie-single-tytul column-12">
                     <?php echo _e( $title , 'PP2014' ); ?>
                </h1>     	
                <?php 
                	if($wydarzenieArchiwalne == true){
                		?>
                			<div class="termin-single termin-single-archiwum">
                		<?php
                	}
                	else{
                		?>
                			<div class="termin-single" style="background-color:<?php echo $kolor_tla_naPodstLokalizacji ?>">
                		<?php
                	}
                ?>
               
               	  <div class="termin-single-czas clearfix">
                  	<!-- div.termin-single-czas zawiera etykietę <h2>Termin wydarzenia:</h2> i dane o terminie - tj. czasie wydarzenia
                    		potrzebne to jest do stylizowania paska na węższą stronę, by mógł być poziomy -->
               		<h2>Termin wydarzenia:</h2>
               		<?php
               		if(!empty($termin_opisowy))
								//jeśli jest to termin opisowy
								{
							?>		<!--Zawartość div.termin-single-->
                                        <div class="termin-opisowy">
                                            <p><?php echo $termin_opisowy ?></p>
                                        </div>
                                    <!--Koniec zawartości div.termin-single-->
							<?php
								}//if(!empty($termin_opisowy))
								else if(!empty($dzien_zakonczenia) && !empty($dzien_rozpoczecia))
								//jeśli są wypełnione dzień zakończenia i dzień rozpoczęcia to jest to termin od - do
								{
							?>
                            		<!--Zawartość div.termin-single-->
                                        <div class="termin-od">
                                            <p class="od-do">Od <?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_rozpoczecia), TRUE) ?></p>
                                            <p class="dzien">
                                                <?php echo pobieczCzescDaty('j',$dzien_rozpoczecia) ?>
                                            </p>
                                            <p><?php echo ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$dzien_rozpoczecia)); ?><br />
                                                <?php echo pobieczCzescDaty('Y',$dzien_rozpoczecia); ?>
                                            </p>
                                        </div>
                                        <div class="termin-do">
                                            <p class="od-do">Do <?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_zakonczenia), TRUE) ?></p>
                                            <p class="dzien">
                                                <?php echo pobieczCzescDaty('j',$dzien_zakonczenia) ?>
                                            </p>
                                            <p><?php echo ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$dzien_zakonczenia)); ?><br />
                                                <?php echo pobieczCzescDaty('Y',$dzien_zakonczenia); ?>
                                            </p>
                                        </div>
                                      <!--Koniec zawartości div.termin-single-->
							<?php
								}//else if(!empty($dzien_zakonczenia) && !empty($dzien_rozpoczecia))
								else if(!empty($dzien_rozpoczecia))
								//jeśli jest wypełniony tylko $dzien_rozpoczecia, bez $dzien_zakonczenia to jest to wydarzenie jednodniowe
								{
							?>
                            	<!--Zawartość div.termin-single-->
                                    <div class="termin-jednodniowy">
                                    	<p> <?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_rozpoczecia)) ?></p>
                                        <p class="dzien">
											<?php echo pobieczCzescDaty('j',$dzien_rozpoczecia) ?>
                                        </p>
                                        <p><?php echo ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$dzien_rozpoczecia)); ?><br />
                                        	<?php echo pobieczCzescDaty('Y',$dzien_rozpoczecia); ?>
										</p>
                                    </div>
                                <!--Koniec zawartości div.termin-single-->
							<?php
								}//if(!empty($dzien_rozpoczecia))
								else if(!empty($data_i_godzina_wydarzenia))
								//jeśli nie wypełnione żadne powyższe brana jest pod uwagę $data_i_godzina_wydarzenia (zwykłe wydarzenie)
								//sprawdzanie czy jest empty powinno być tu formalnością, bo nie da się dodać wydarzenia bez wypełnienia tego pola
								{
							?>
                            		<!--Zawartość div.termin-single-->
                            		
                                        <div class="termin-dzien">
                                            <p> <?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$data_i_godzina_wydarzenia)) ?></p>
                                            <p class="dzien">
                                                <?php echo pobieczCzescDaty('j',$data_i_godzina_wydarzenia) ?>
                                            </p>
                                            <p><?php echo ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$data_i_godzina_wydarzenia)); ?><br />
                                                <?php echo pobieczCzescDaty('Y',$data_i_godzina_wydarzenia); ?>
                                            </p>
                                        </div>
                                        <div class="termin-godz">
                                            <p>godz:</p>
                                            <p class="godzina">
                                                <?php echo pobieczCzescDaty('G',$data_i_godzina_wydarzenia) ?>
                                                <span class="minuty">
                                                	<?php echo pobieczCzescDaty('i',$data_i_godzina_wydarzenia) ?>
                                                </span>
                                            </p>
                                        </div>
                                        
                                        

							<?php
								}//else if(!empty($data_i_godzina_wydarzenia))
								else
								//jeśli nie jest to żaden ze znanych rodzajów terminów
								{
							?>		<!--Zawartość div.termin-single-->
                                            <p>Błąd terminu</p>
                                    <!--Koniec zawartości div.termin-single-->
							<?php
								}//else
								
								?>
                          </div><!--.termin-single-czas --> 
                                <!-- KUP BILET, TYLKO W KASIE, BRAK BILETÓW itp. -->
                            <div class="termin-bilety">
							<?php 
								if($wydarzenieArchiwalne == true){
									echo '<h2 class="wydarzenie_archiwalne">Wydarzenie archiwalne</h2>';
								}
								else if(!empty($inny_komunikat_o_biletach)){
								//jeśli pole inny_komunikat_o_biletach nie jest pusty to wyświetlana jest jego treść a cała reszta pomijana
									echo '<h2>'.$inny_komunikat_o_biletach.'</h2>';;
								}//if(!empty($inny_komunikat_o_biletach))
								else{
									//Pobranie ceny biletu wyświetlanej w przycisku "Kup bilet" oraz przy Bilety do nabycia tylko w kasie:
									$cena_biletu_opisowa = $pods->display('cena_biletu_opisowa');
									$cena_biletu = $pods->display('cena_biletu');
									
									if(empty($cena_biletu_opisowa)){
									//Jeśli nie wypełniono ceny opisowej to pobierana cena kwotowa
									
										if($cena_biletu > 0){
											//Jeśli cena kwotowa nie jest pusta to jest brana pod uwagę, w przeciwnym wypadku nic nie jest doklejane do przycisku
											$cena = $cena_biletu.' zł';
										}//if(!empty($cena_biletu))
									}//if(empty($cena_biletu_opisowa))
									else{
									//Jeśli podano cenę kwotową, to ona jest brana pod uwagę
										$cena = "<br>$cena_biletu_opisowa";
									}//else od if(empty($cena_biletu_opisowa))
									
									switch ($opcje_sprzedazy) {
										case "wstep_wolny":
											echo '<h2>Wstęp wolny</h2>';
											break;
										case "brak_biletow":
											echo '<h2>Brak dostępnych biletów</h2>';
											break;
										case "tylko_kasa":
											echo '<h2>Bilety: '.$cena.'</h2>';
											echo '<h2><a class="bilety-kasa" href="'.home_url().'/o-nas/kasa/">Bilety do nabycia tylko w kasie OWE "Odra"</a></h2>';
											break;
										default:
											$termin_publikacji_odnosnika = new DateTime($dzien_publikacji_odnosnika_do_biletow.' '.$godzina_publikacji_odnosnika_do_biletow);
											//$termin_publikacji_odnosnika  = new DateTime();
											$teraz = pobierzDateTeraz();
										   if(empty($dzien_publikacji_odnosnika_do_biletow) || $termin_publikacji_odnosnika < $teraz){
											   //jeśli minął już termin publikacji odnośnika lub nie wybrano dnia publikacji (równoznaczne
											   //z opublikowaniem
											   if($id_w_sprzedazy_online > 0){
												   //jeśli podano konkretny ID w sprzedaży
											   		echo '<h2>Bilety: '.$cena.'</h2>';
													echo '<a class="kup-bilet" href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'">Kup bilety</a>';
											   }
											   else{
												   //jeśli nie podano konkretnego ID w sprzedaży - link do całego repertuaru w sprzedaży
												   echo '<h2>Bilety: '.$cena.'</h2>';													
												   echo '<a class="kup-bilet" href="'.home_url().'/bilety-online/">Kup bilety</a>';
											   }
										   }
										   else{
											   
											   //użycue funkcji zamienDateGodzinePodsNaTekst - chcąc ustawić trzeci parametr (bez wyświetlania roku) na TRUE muszę ustawić drugi na NULL
												echo '<h2>Bilety w sprzedaży<br> od '.zamienDateGodzinePodsNaTekst($termin_publikacji_odnosnika->format('Y-m-d'), NULL, TRUE).'</h2>';
																									   }
									}
									
								}//else od if(!empty($inny_komunikat_o_biletach))
							
							?>
                            </div><!--.wydarzenie-bilety-->
                                
                                <!-- LOKALIZACJA WYDARZENIA-->
                                <?php if(!empty($lokalizacje_adres_slug)){
										//jeśli jest zdefiniowana lokalizacja dla wydarzenia wyświetla informację o niej i odnośnik na pasku bocznym
									?>

									<div class="termin-lokalizacja">
                                    	<h2>Lokalizacja wydarzenia:</h2>
                                                <a href="<?php echo home_url().'/adresy/'.$lokalizacje_adres_slug ?>">
                                                <?php 	echo '<p class="nazwa_lokalizacji">'.$lokalizacje_krotka_nazwa.'</p>';
                                                        echo $lokalizacje_adres_nazwa.'<br />';
                                                        echo $lokalizacje_adres_adres.'<br />';
                                                        echo $lokalizacje_adres_kod_pocztowy.' '.$lokalizacje_adres_miejscowosc;
                                                     ?></a>
                                    
                                    <?php
                                    
                                    if (( !is_null($picture) )&&(!empty($picture))){
                                        echo $picture;
                                    }
                                    ?>
                                    </div><!--.termin-lokalizacja-->

                                <?php }//if(empty($lokalizacje_adres_slug)){ ?>
                           
               </div><!--.termin-single-->
               
               <article class="wydarzenie-single column-9">
                    	<div class="wydarzenie-single-tresc">
                        	<?php echo $post_content; ?>
                            
                            <!-- DOPISEK POD TREŚCIĄ O DOSTĘPNOŚCI BILETÓW -->
                            <!--$inny_komunikat_o_biletach
                            $wstep_wolny==true
                            $sprzedaz_tylko_kasa
                            $id_w_sprzedazy_online > 0-->
                            
                            <?php
								if(!empty($inny_komunikat_o_biletach)){
									echo '<strong>'.$inny_komunikat_o_biletach.'</strong>';
								}//if(!empty($inny_komunikat_o_biletach))
								else{
									if(!$wstep_wolny){
										if($id_w_sprzedazy_online > 0 || $sprzedaz_tylko_kasa)
										{
											echo '<strong>Bilety do nabycia w <a href="'.home_url().'/o-centrum/kasa-biletowa/">kasie OWE Odra</a>';
											
											if(!$sprzedaz_tylko_kasa && $id_w_sprzedazy_online > 0){
												echo ' i <a href="'.home_url().'/bilety-online/">online</a>.';
											}//if(!$sprzedaz_tylko_kasa && $id_w_sprzedazy_online > 0)
											
											echo '</strong>';

										}//if($id_w_sprzedazy_online > 0 || $sprzedaz_tylko_kasa)
									}
								}//else - if(!empty($inny_komunikat_o_biletach))
							?>
                        </div>
                </article>
                 	
				<?php
				}//if ($pods->exists())
			}//(is_single())
			else{
				//Jeśli nie jest to strona pojedynczego wydarzenia - błąd. Zostaje to wyświetlone.
				?>
                <p>Błąd szablonu pojedynczego wydarzenia WYDARZENIA_SINGLE. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od (is_single())
		?>
		</section><!-- #content-container -->
        <?php //get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>