<?php get_header(); ?>
	<div id="main-wrap">
        <div id="main-container" class="clearfix">
            <section id="content-container" class="column-9-scalable">
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
                        
            </section><!--#content-container-->
            <?php get_sidebar(); ?>
        </div><!--#main-container-->
	</div><!--#main-wrap - koniec-->
<?php get_footer(); ?>