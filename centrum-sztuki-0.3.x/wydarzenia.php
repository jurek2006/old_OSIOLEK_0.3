<?php
/*
Template Name: Wydarzenia
Description: Obsługuje stronę listy wydarzeń stylu Centrum Sztuki w Oławie korzystając z pods wydarzenia oraz pods kategorie (dla kategorii wydarzeń).
Pojedyncze wydarzenie obsługiwane są przez wydarzenia_single.php
*/

get_header(); ?>

<!--Fragment dla listy wydarzeń - dodaje pasek wyświetlający jaka jest wybrana kategoria i umożliwiający jej zmianę -->

<?php
			if (!is_single()){
			//Na wszelki wypadek sprawdza czy to jest lista wydarzeń
				
				//pobranie listy zdefiniowane_kategorie_wydarzen z pods kategorie
				//lista ta posłuży do sprawdzenia, czy wybrano którąś ze zdefiniowanych kategorii wydarzeń
				//jeśli nie, traktowane jest to, jako wyświetlanie wszystkich wydarzeń
				//jeśli tak - tworzony jest pasek wyświetlający wybraną kategorię i umożliwiający jej zmianę
				$slug = pods_v('last','url');
				$zdefiniowane_kategorie_wydarzen = array();
				//tabela przechowująca nazwy kategorii, żeby wyświetlać je, zamiast slug'a
				$zdefiniowane_kategorie_wydarzen_nazwy = array();
				
				$params = array( 'limit' => -1);
				$pods1 = pods( 'kategorie', $params );
				if ( $pods1->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
                    while ( $pods1->fetch() ) {
						$slug = $pods1->display('slug');
						$zdefiniowane_kategorie_wydarzen[] = $slug;
						$nazwa = $pods1->display('name');
						$zdefiniowane_kategorie_wydarzen_nazwy[] = $nazwa;
					}
				}
				
				
				$slug = pods_v('last','url'); //slug strony
				if(in_array($slug,$zdefiniowane_kategorie_wydarzen)){
					//'Wybrana kategoria: '.$slug;
					
					$params = array( 	'limit' => -1,
									'where'   => '((wydarzenie_kategorie.slug LIKE "%'.$slug.'%")AND((data_i_godzina_wydarzenia.meta_value > NOW()) OR (dzien_zakonczenia.meta_value > NOW())))',
									'orderby'  => 'data_i_godzina_wydarzenia.meta_value');
									
					//Pobranie nazwy wybranej kategorii - sprawdzamy jaki index w $zdefiniowane_kategorie_kursow ma wybrany $slug
					//A jako, że $zdefiniowane_kategorie_wydarzen_nazwy ma tą samą kolejność to pobierany z niego pole o tym indexie
					$nazwa_kategorii = $zdefiniowane_kategorie_wydarzen_nazwy[array_search($slug, $zdefiniowane_kategorie_wydarzen)];
				}
				else{
					//Wyświetlanie wszystkich wydarzeń
					
					$params = array( 	'limit' => -1,
									'where'   => '(data_i_godzina_wydarzenia.meta_value > NOW()) OR (dzien_zakonczenia.meta_value > NOW())',
									'orderby'  => 'data_i_godzina_wydarzenia.meta_value');
									
					//podstawienie pod slug na potrzeby wyświetlenia na pasku zmiany kategorii				
					$nazwa_kategorii = 'wszystkie'; 
				}
				
				//Stworzenie paska
					?>
                    	<div id="kategorie-wrap">
                        	<div id="kategorie-container">
                            	                          
                                <?php
								$menu_class = 'category-nav'; //standardowa (niemobilna klasa dla menu kategorii)
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
                                        'theme_location' => 'category-navigation',
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
				

                
                //get pods object
				//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
				$pods = pods( 'wydarzenia', $params );
                //loop through records
                if ( $pods->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
                    while ( $pods->fetch() ) {
                        //Put field values into variables
                        $title = $pods->display('name');
						$permalink = $pods->field('permalink' );
                        $picture = $pods->field('miniatura');
						
						//pobieranie koloru tła na podstawie lokalizacji
						$kolor_tla_naPodstLokalizacji = $pods->field('lokalizacje.adres.kolor');
						$kolor_tla_naPodstLokalizacji = $kolor_tla_naPodstLokalizacji[0];
						
						
						//Tworzenie linijki wpisu terminu
						$data_i_godzina_wydarzenia = $pods->display('data_i_godzina_wydarzenia');
						$dzien_rozpoczecia = $pods->display('dzien_rozpoczecia');
						$dzien_zakonczenia = $pods->display('dzien_zakonczenia');
						$termin_opisowy = $pods->display('termin_opisowy');
						$wystawa_z_wernisazem = $pods->field('wystawa_z_wernisazem');
						$alias_wernisazu = $pods->display('alias_wernisazu');
						
						$termin = pobierzTerminWydarzenia($data_i_godzina_wydarzenia, $dzien_rozpoczecia, $dzien_zakonczenia, $termin_opisowy, $alias_wernisazu, $wystawa_z_wernisazem);
						
						$miniatura = $pods->display('miniatura');
						$lokalizacje = $pods->field('lokalizacje.name');
						$lokalizacje = $lokalizacje[0];

						$lokalizacje_adres = $pods->field('lokalizacje.adres.adres');
						$lokalizacje_adres = $lokalizacje_adres[0];
						
						$lokalizacje_slug = $pods->field('lokalizacje.slug');
						

						$kategorie_name = $pods->field('wydarzenie_kategorie.name');
						$kategorie_slug = $pods->field('wydarzenie_kategorie.slug');
						
						$krotki_opis = $pods->field('krotki_opis');
						
						$inny_komunikat_o_biletach = $pods->display('inny_komunikat_o_biletach');
						$opcje_sprzedazy = $pods->field('opcje_sprzedazy');
						$id_w_sprzedazy_online = $pods->field('id_w_sprzedazy_online');
						$dzien_publikacji_odnosnika_do_biletow = $pods->field('dzien_publikacji_odnosnika_do_biletow');
						$godzina_publikacji_odnosnika_do_biletow = $pods->field('godzina_publikacji_odnosnika_do_biletow');
						if(!empty($dzien_publikacji_odnosnika_do_biletow) && $godzina_publikacji_odnosnika_do_biletow==0){
							//jeśli wybrano dzień publikacji odnośnika, a nie wybrano godziny (wybrano 00:00) to wstawiana jest standardowa 10:00
							$godzina_publikacji_odnosnika_do_biletow = '10:00';
						}
						
						$home_url = get_home_url();
						
						
						            ?>
            			<a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"></a>
                        <article class="wydarzenie column-12">
                        	<div class="wydarzenie-pasek">
                                <div class="termin" style="background-color:<?php echo $kolor_tla_naPodstLokalizacji ?>">
                                <?php
                                    if(!empty($termin_opisowy))
                                    //jeśli jest to termin opisowy
                                    {
                                ?>		<!--Zawartość div.termin-->
                                            <div class="termin-opisowy">
                                                <p><?php echo $termin_opisowy ?></p>
                                            </div>
                                            <div class="termin-lokalizacja">
                                                    <p><?php echo $lokalizacje; ?><br /><?php echo $lokalizacje_adres; ?></p>
                                            </div>
                                        <!--Koniec zawartości div.termin-->
                                <?php
                                    }//if(!empty($termin_opisowy))
                                    else if(!empty($dzien_zakonczenia) && !empty($dzien_rozpoczecia))
                                    //jeśli są wypełnione dzień zakończenia i dzień rozpoczęcia to jest to termin od - do
                                    {
                                ?>
                                        <!--Zawartość div.termin-->
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
                                            <div class="termin-lokalizacja">
                                                <p><?php echo $lokalizacje; ?><br /><?php echo $lokalizacje_adres; ?></p>
                                            </div>
                                          <!--Koniec zawartości div.termin-->
                                <?php
                                    }//else if(!empty($dzien_zakonczenia) && !empty($dzien_rozpoczecia))
                                    else if(!empty($dzien_rozpoczecia))
                                    //jeśli jest wypełniony tylko $dzien_rozpoczecia, bez $dzien_zakonczenia to jest to wydarzenie jednodniowe
                                    {
                                ?>
                                    <!--Zawartość div.termin-->
                                        <div class="termin-jednodniowy">
                                            <p> <?php echo zamienDzienTygodniaLiczbowyNaSlowny(pobieczCzescDaty('w',$dzien_rozpoczecia)) ?></p>
                                            <p class="dzien">
                                                <?php echo pobieczCzescDaty('j',$dzien_rozpoczecia) ?>
                                            </p>
                                            <p><?php echo ZamienMiesiacLiczbowyNaSlownyOdmieniony(pobieczCzescDaty('m',$dzien_rozpoczecia)); ?><br />
                                                <?php echo pobieczCzescDaty('Y',$dzien_rozpoczecia); ?>
                                            </p>
                                        </div>
                                        <div class="termin-lokalizacja">
                                                <p><?php echo $lokalizacje; ?><br /><?php echo $lokalizacje_adres; ?></p>
                                        </div>
                                    <!--Koniec zawartości div.termin-->
                                <?php
                                    }//if(!empty($dzien_rozpoczecia))
                                    else if(!empty($data_i_godzina_wydarzenia))
                                    //jeśli nie wypełnione żadne powyższe brana jest pod uwagę $data_i_godzina_wydarzenia (zwykłe wydarzenie)
                                    //sprawdzanie czy jest empty powinno być tu formalnością, bo nie da się dodać wydarzenia bez wypełnienia tego pola
                                    {
                                ?>
                                        <!--Zawartość div.termin-->
                                        
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
                                            <div class="termin-lokalizacja">
                                                <p><?php echo $lokalizacje; ?><br /><?php echo $lokalizacje_adres; ?></p>
                                            </div>
                                        <!--Koniec zawartości div.termin-->
                                <?php
                                    }//else if(!empty($data_i_godzina_wydarzenia))
                                    else
                                    //jeśli nie jest to żaden ze znanych rodzajów terminów
                                    {
                                ?>		<!--Zawartość div.termin-->
                                                <p>Błąd terminu</p>
                                        <!--Koniec zawartości div.termin-->
                                <?php
                                    }//else
                                    
                                ?>
                                </div><!--.termin-->
                                
                                <div class="termin-thumb">
                                    <?php 
                                    //jeśli wydarzenie ma przypisaną miniaturę, to jest ona wyświetlana
                                    if (( !is_null($picture) )&&(!empty($picture))){
                                        echo wp_get_attachment_image( $picture['ID'] ); 
                                    }
                                    //jeśli wydarzenie nie ma przypisanej miniatury to jest wyświetlany standardowy obrazek pegaz_thumb.jpg
                                    else{
                                        echo '<img src="'.get_template_directory_uri().'/pegaz_thumb.png" />';	
                                    }?>
                                 </div><!--termin-thumb-->
                                
                                <div class="wydarzenie-tresc">
                                    <h1 class="wydarzenie-tytul">
                                             <?php _e( $title , 'PP2014' ); ?>
                                    </h1>
                                    <div class="wydarzenie-kategorie">
                                        <?php for($i=0; $i < count($kategorie_name); $i++){
                                            if(!empty($kategorie_slug[$i]))
                                            {
                                                echo '<a href="'.home_url().'/kategorie_wydarzen/'.$kategorie_slug[$i].'/" class="kategoria">'.$kategorie_name[$i].'</a> ';
                                            }
                                        }?>
                                        
                                        
                                    </div>
                                    <div class="wydarzenie-opis">
                                        <?php echo $krotki_opis; ?>
                                    </div>
                                </div><!--.wydarzenie-tresc-->
                                
                                <a class="wydarzenie-czytajWiecej" href="<?php echo esc_url( $permalink); ?>"><div>Czytaj<br />
    więcej</div></a>
							</div><!--.wydarzenie-pasek-->
                            
							<!-- KUP BILET, TYLKO W KASIE, BRAK BILETÓW itp. -->
                            <div class="wydarzenie-bilety">
                            
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
														//$termin_publikacji_odnosnika  = new DateTime();
														$teraz = pobierzDateTeraz();
													   if(empty($dzien_publikacji_odnosnika_do_biletow) || $termin_publikacji_odnosnika < $teraz){
														   //jeśli minął już termin publikacji odnośnika lub nie wybrano dnia publikacji (równoznaczne
														   //z opublikowaniem
														   if($id_w_sprzedazy_online > 0){
															   //jeśli podano konkretny ID w sprzedaży
														   		echo '<span class="kup-bilet"><a href="http://www.systembiletowy.pl/cso/index.php/repertoire.html?id='.$id_w_sprzedazy_online.'">Kup bilety</a></span>';
														   }
														   else{
															   //jeśli nie podano konkretnego ID w sprzedaży - link do całego repertuaru w sprzedaży
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
                            
                            
                            </div><!--.wydarzenie-bilety-->
                        </article>
            <?php
					}//while ( $pods->fetch() )
				}//if ( $pods->total() > 0 )
				else{
				//jeśli nie znaleziono wydarzeń spełniających określone kryteria
					?>
                    <h2>Brak zaplanowanych wydarzeń z kategorii <?php echo $nazwa_kategorii ?>.</h2>
                    <p>W najbliższym czasie Centrum Sztuki w Oławie nie organizuje żadnych wydarzeń z kategorii <?php echo $nazwa_kategorii ?>.</p>
					<p>Zapraszamy do zapoznania się z ofertą naszych wydarzeń z innych kategorii:<br />

					<?php
						
						echo '<a href="'.home_url().'/">wszystkie kategorie</a> ';
						//wypisanie odnośników do wszystkich innych kategorii poza aktualnie otwartą (w której nic nie znaleziono)
						for($i = 0; $i < count($zdefiniowane_kategorie_wydarzen_nazwy); $i++)
						{
							if($zdefiniowane_kategorie_wydarzen_nazwy[$i] != $nazwa_kategorii)
							//jeśli dana kategoria nie jest kategorią dla której właśnie nie znaleziono żadnych wydarzeń to wyświetlany odnośnik do niej
							{
								echo '<a href="'.home_url().'/kategorie_wydarzen/'.$zdefiniowane_kategorie_wydarzen[$i].'/">'.$zdefiniowane_kategorie_wydarzen_nazwy[$i].'</a> ';
							}
						}//for($i = 0; $i < count($zdefiniowane_kategorie_wydarzen_nazwy); $i++)
					?>
                    </p>
					<?php
				}//else od if ( $pods->total() > 0 )
			}//if (!is_single())
			else{
				//Jeśli nie jest to lista wydarzeń zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
				?>
                	<p>Błąd listy wydarzeń szablonu WYDARZENIA. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od if (!is_single())
		?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>