<?php
/*
Template Name: OUTW_Aktualnosci
Description: Obsługuje stronę listę aktualności OUTW

*/

get_header(); ?>


<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container" class="column-9-scalable">
        <h1>Aktualności OUTW</h1>
		<?php
			if (!is_single()){
			//Na wszelki wypadek sprawdza czy to jest lista a nie pojedynczy wpis
				
				$params = array( 	'limit' => -1,
									'orderby'  => 'data.meta_value DESC');
                
                //get pods object
				//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
				$pods = pods( 'outw_aktualnosci', $params );
                //loop through records
                if ( $pods->total() > 0 ) {
					//jeśli znaleziono wydarzenia spełniające określone kryteria - następuje wyświetlenie ich listy
                    while ( $pods->fetch() ) {
                        //Put field values into variables
                        $title = $pods->display('name');
						$post_content = $pods-> display('post_content');
						$data_ostatniej_aktualizacji = $pods-> display('data');
						?>
                        
                        <article class="OUTW_aktualnosci">
							<h2><?php echo $title ?></h2>
                            <?php if(!empty($data_ostatniej_aktualizacji)){  
								echo '<span>'.zamienDateNaTekst($data_ostatniej_aktualizacji).'</span>';
								}//if(!empty($data_ostatniej_aktualizacji))
							?>
                            <?php echo $post_content; ?>
                        </article>
            <?php
					}//while ( $pods->fetch() )
				}//if ( $pods->total() > 0 )
				else{
				//jeśli nie znaleziono wydarzeń spełniających określone kryteria
					?><p>Nie znaleziono żadnych aktualności.</p><?php
				}//else od if ( $pods->total() > 0 )
			}//if (!is_single())
			else{
				//Jeśli nie jest to lista wydarzeń zgłasza błąd. Plikiem odpowiadającym za wyświetlanie pojedynczych wydarzeń jest wydarzenia_single.php
				?>
                	<p>Błąd listy OUTW Aktualności szablonu OUTW_AKTUALNOSCI. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od if (!is_single())
		?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>