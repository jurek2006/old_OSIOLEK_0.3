<?php
/*
Template Name: Edukacja Single
Description: Obsługuje strony pojedynczych kursów stylu Centrum Sztuki w Oławie korzystając z pods kursy i grupy_kursowe oraz kategorie_kursow (dla kategorii kursów).
Listy kursów obsługiwane są przez edukacja.php
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
				$pods = pods( 'kursy', $slug );
				
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
				
				//szablon opłat
				$oplata_szablon = $pods->display('oplata_szablon.szablon');
				
				$post_content = $pods-> display('post_content');
				?>
				<article class="wydarzenie-single column-9">
					<header>
						<h1 class="wydarzenie-tytul">
							 <?php echo _e( $title , 'PP2014' ); ?>
						</h1>
                        	
					</header>
                    	
                    <!--rozpoczęcie tabeli-->
                    <table id="tabelaKursow" class="column-12" frame="void" cellspacing="0" cols="4" rules="none">
						<tbody>
							<?php
								//rysowanie wiersza kursu bez nazwy kursu - odnośnika
                                rysujWierszTabeliKursow($pods, $kolor_tabeli_kursu, false);
                             ?>
                        <!--zakończenie tabeli-->
                    	</tbody>
                    </table>
     					
                    	<div class="wydarzenie-single-tresc">
                        	<?php echo $post_content; 
								echo $oplata_szablon;
							?>
                            
                        </div>
                </article>
                
               <div class="termin-single edukacja-single" style="background-color:<?php echo $kolor_tla_naPodstLokalizacji ?>">
               		

                                
                                <!-- LOKALIZACJA ZAJĘĆ-->
                                <h2>Lokalizacja zajęć:</h2>
                                <div class="termin-lokalizacja" style="position:relative">
                                        	
                                            <a style="margin-bottom: 10px" href="<?php echo home_url().'/adresy/'.$lokalizacje_adres_slug ?>">
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
                            
               </div><!--.termin-single-->
                 	
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