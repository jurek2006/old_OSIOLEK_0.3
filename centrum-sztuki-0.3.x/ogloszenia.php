<?php
/*
Template Name: Ogloszenia
Description: Obsługuje stronę listę przetargów i zamówień w O Centrum/Ogłoszenia. Wyświetlane są tylko ogłoszenia, których minęła data publikacji. Dla zalogowanych użytkowników mających uprawnienia do edytowania postów (od Autora wzwyż) wyświetlana jest też lista ogłoszeń jeszcze nieopublikowanych.

*/

get_header(); ?>


<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container" class="column-9-scalable">
        <h1>Ogłoszenia Centrum Sztuki w Oławie</h1>
		<?php
			if (!is_single()){
			//Na wszelki wypadek sprawdza czy to jest lista a nie pojedynczy wpis
				
				$params = array( 	'limit' => -1,
									'where'   => 'termin_opublikowania.meta_value < NOW()',
									'orderby'  => 'termin_opublikowania.meta_value DESC');
                
                //get pods object
				//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
				$pods = pods( 'ogloszenia', $params );
                //loop through records
                if ( $pods->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
                    while ( $pods->fetch() ) {
                        //Put field values into variables
                        $title = $pods->display('name');
						$post_content = $pods-> display('post_content');
						$data_ostatniej_aktualizacji = $pods-> display('data_ostatniej_aktualizacji');
						$kategorie = $pods-> display('kategorie');
						$permalink = $pods->field('permalink' );
						$krotki_opis = $pods-> display('krotki_opis');
						$termin_opublikowania = $pods-> display('termin_opublikowania');
						?>
                        
                        <article class="przetarg_zamowienie_ogloszenie">
                        	<span> Kategorie: <?php echo $kategorie ?></span>
							<h2><a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"><?php echo $title ?></a></h2>
                            <?php if(!empty($termin_opublikowania)){  
								//echo '<span>'.zamienDateNaTekst($data_ostatniej_aktualizacji).'</span>';
								$termin_opublikowania = explode(' ', $termin_opublikowania);
								echo '<span>'.zamienDateNaTekst($termin_opublikowania[0]).'</span>';
								
								}//if(!empty($data_ostatniej_aktualizacji))
								else{
									echo 'empty';
								}
							?>
                            <strong><?php echo $krotki_opis; ?></strong>
                            <a href="<?php echo esc_url( $permalink); ?>">Szczegóły ogłoszenia</a>
                        </article>
                        
                        
            <?php
					}//while ( $pods->fetch() )
				}//if ( $pods->total() > 0 )
				else{
				//jeśli nie znaleziono wydarzeń spełniających określone kryteria
					?><p>Przepraszamy ale nie ma żadnego aktualnego ogłoszenia.</p><?php
				}//else od if ( $pods->total() > 0 )
			}//if (!is_single())
			else{
				//Jeśli nie jest to lista wydarzeń zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
				?>
                	<p>Błąd listy ogłoszeń OGŁOSZENIA. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od if (!is_single())
		?>
        </section><!-- #content-container -->
        
        <!--CZĘŚĆ TYLKO DLA UŻYTKOWNIKACH O UPRAWNIENIACH DO EDYTOWANIA POSTÓW - wyświetla ogłoszenia jeszcze nieopublikowane-->
        
        <?php
		if (current_user_can( 'publish_posts' )){
			if (!is_single()){
				//Na wszelki wypadek sprawdza czy to jest lista a nie pojedynczy wpis
					
					$params = array( 	'limit' => -1,
										'where'   => 'termin_opublikowania.meta_value > NOW()',
										'orderby'  => 'termin_opublikowania.meta_value DESC');
					
					//get pods object
					//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
					$pods = pods( 'ogloszenia', $params );
					//loop through records
					if ( $pods->total() > 0 ) {
						echo '<section id="content-do-opublikowania" class="column-9-scalable">';
						//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
						while ( $pods->fetch() ) {
							//Put field values into variables
							$title = $pods->display('name');
							$post_content = $pods-> display('post_content');
							$kategorie = $pods-> display('kategorie');
							$permalink = $pods->field('permalink' );
							$krotki_opis = $pods-> display('krotki_opis');
							$termin_opublikowania = $pods-> display('termin_opublikowania');
							?>
                            
                            <h2>Ogłoszenia oczekujące na opublikowanie</h2>
							
							<article class="przetarg_zamowienie_ogloszenie">
								<span> Kategorie: <?php echo $kategorie ?></span>
								<h2><a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"><?php echo $title ?></a></h2>
                                
								<?php if(!empty($termin_opublikowania)){  
									$termin_opublikowania = explode(' ', $termin_opublikowania);
									echo '<span>Ogłoszenie zostanie opublikowane '.zamienDateNaTekst($termin_opublikowania[0]).' o godz. '.$termin_opublikowania[1].'</span>';
									
									}//if(!empty($data_ostatniej_aktualizacji))
								?>
								<strong><?php echo $krotki_opis; ?></strong>
								<a href="<?php echo esc_url( $permalink); ?>">Szczegóły ogłoszenia</a>
							</article>
							
							
				<?php
						}//while ( $pods->fetch() )
						echo '</section>'; /*#content-do-opublikowania */
					}//if ( $pods->total() > 0 )
					else{
					//jeśli nie znaleziono wydarzeń spełniających określone kryteria
						?><p>Brak ogłoszeń do opublikowania</p><?php
					}//else od if ( $pods->total() > 0 )
				}//if (!is_single())
				else{
					//Jeśli nie jest to lista wydarzeń zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
					?>
						<p>Błąd listy ogłoszeń OGŁOSZENIA. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
					<?php
				}//else od if (!is_single())
		}//if (current_user_can( 'publish_posts' ))
		?>
        
        <!--KONIEC CZĘŚCI TYLKO DLA UŻYTKOWNIKÓW UPRAWNIONYCH-->
        
		
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>