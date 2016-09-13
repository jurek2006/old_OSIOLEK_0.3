<?php
/*
Template Name: Ogloszenia Single
Description: Obsługuje strony pojedynczych ogłoszeń stylu Centrum Sztuki w Oławie korzystając z pods ogloszenia 
Lista ogłoszeń obsługiwana jest przez ogloszenia.php
*/

get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container" class="column-9-scalable">
		<?php
			if (is_single()){
			//Na wszelki wypadek sprawdza, czy jest to pojedyncza strona wydarzenia
				
				//pobranie slug aktulnie otwartego wydarzenia i wczytanie dla niego pods'a
				$slug = pods_v('last','url');
				//get pods object
				$pods = pods( 'ogloszenia', $slug );
				
				if ($pods->exists()){
				
				//Put field values into variables
				$title = $pods->display('name');
				$termin_opublikowania = $pods-> display('termin_opublikowania');
				$kategorie = $pods-> display('kategorie');				
				$post_content = $pods-> display('post_content');
				?>
                            
             
               <article class="przetarg_zamowienie_ogloszenie">
               		<span> Kategorie: <?php echo $kategorie ?></span>
               		<h1><?php echo _e( $title , 'PP2014' ); ?></h1> 
                     
                     <?php if(!empty($termin_opublikowania)){  
									$termin_opublikowania = explode(' ', $termin_opublikowania);
									echo '<span>'.zamienDateNaTekst($termin_opublikowania[0]).'</span>';
									
									}//if(!empty($data_ostatniej_aktualizacji))
							echo $post_content;
					?>
                            
                </article>
                 	
				<?php
				}//if ($pods->exists())
			}//(is_single())
			else{
				//Jeśli nie jest to strona pojedynczego wydarzenia - błąd. Zostaje to wyświetlone.
				?>
                <p>Błąd szablonu pojedynczego ogłoszenia OGLOSZENIA-SINGLE. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p>
				<?php
			}//else od (is_single())
		?>
		</section><!-- #content-container -->
        <?php get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>