<?php get_header(); ?>
	<div id="main-container">
    	<section id="content-container">
        	<?php
				//Początek pętli
				while( have_posts() ) : the_post(); 
			
            	//<!--TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
            	//Pobranie odpowiedniego typu treści
				get_template_part('content', 'single' );
                
				//komentarze
				comments_template('',true);
				
				
            //<!--koniec TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
            
				//Koniec pętli
				endwhile;
			?>    
            		
        </section><!--#content-container-->
    	<?php get_sidebar(); ?>
    </div><!--#main-container-->
<?php get_footer(); ?>