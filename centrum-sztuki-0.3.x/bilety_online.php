<?php
/*
Template Name: Bilety_Online
Description: Obsługuje stronę listę przetargów i zamówień w O Centrum/Przetargi i zamówienia

*/

get_header(); ?>


<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container" class="column-12">
        
         <?php
                    
                    //Początek pętli
                    while( have_posts() ) : the_post(); 
                        
                    //echo '<article class="column-9">';
                    //<!--TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
                    //Pobranie odpowiedniego typu treści
                        get_template_part('content', 'single' );
                    //echo '</article>';
                    
                    
                    
                //<!--koniec TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
                
                    //Koniec pętli
                    endwhile;
                ?>
        <br />

			<iframe src="http://www.systembiletowy.pl/cso/" width="1140" height="1550" style="margin-left:0px"> <a href="http://www.systembiletowy.pl/cso/" title="System biletowy">System biletowy</a> </iframe>
		</section><!-- #content-container -->
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>