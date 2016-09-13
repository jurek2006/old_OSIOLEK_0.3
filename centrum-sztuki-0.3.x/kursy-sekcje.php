<?php
/*
Template Name: Kursy i sekcje
*/

get_header(); ?>

	<div id="main-container">
    	<section id="content-container">
            	<table class="tabela_kursow">

						<?php		
						if(is_single()){
						//jeśli jest to pojedyncza strona
							$slug = pods_v('last','url');
							$pod_name = pods_v(1,'url');

							//get pods object
							$pods = pods( $pod_name, $slug );
							
							if ($pods->exists()){
							//Jeżeli znaleziono odpowiedni pods (pojedynczy)
							//sprawdzanie alternatywy nie jest potrzebne - w przypadku błędnego adresu pojedynczej strony strona działa jak niespełnienie warunku is_single - pokazuje listę wpisów
									$title = $pods->display('name');
									$permalink = $pods->field('permalink' );
									$prowadzacy = $pods->display('prowadzacy' );
									$lokalizacje = $pods->display('lokalizacje');
									
									//wyświetlenie tabelki szczegółów z pliku
									include('kursy-sekcje_template.php');
									
									echo $pods->field('post_content');
							}//if ($pods->exists())
						}//if(is_single())
						else{
						//jeśli nie jest to pojedyncza strona - tylko lista
								
							//set find parameters
							$params = array( 	'limit' => -1,
												'orderby' => 'kolejnosc_na_stronie.meta_value ASC');
							//get pods object
							$pods = pods( 'kursy_sekcje', $params );
							//loop through records
							if ( $pods->total() > 0 ) {
								while ( $pods->fetch() ) {
									//Put field values into variables
									$title = $pods->display('name');
									$permalink = $pods->field('permalink' );
									$prowadzacy = $pods->display('prowadzacy' );
									$lokalizacje = $pods->display('lokalizacje');
									
							//wyświetlenie tabelki szczegółów z pliku
							include('kursy-sekcje_template.php');		

                    } //endwhile
                } //endif

                //do the pagination
                echo $pods->pagination( array( 'type' => 'advanced' ) );
						}//else if(is_single)
            ?>
            </table><!--.tabela_kursow-->
			
</section><!--#content-container-->
          
        
    	<?php get_sidebar(); ?>
    </div><!--#main-container-->
<?php get_footer(); ?>