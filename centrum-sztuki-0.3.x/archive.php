<?php get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">

    	<section id="content-container">
        	<?php
				//Początek pętli
				if( have_posts() ) : while( have_posts() ) : the_post(); 
				//wyświetlenie daty w jednym miejscy na każdej stronie
				the_date('','<h3 class="the-date">','</h3>');
			
            	//<!--TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
            	//Pobranie odpowiedniego typu treści
				get_template_part('content', get_post_format() );
                
				//Wczytanie komentarzy, jeśli strona zawiera pojedynczy artykuł
				if( is_singular() ){
					comments_template('',true);
				}
				
            //<!--koniec TREŚĆ WYŚWIETLONA PRZEZ PĘTLĘ-->
            
				//Koniec pętli
				endwhile;
				//Pętla nic nie zwróciła
				else :
			?>    
            		<article id="post-0" class="post no-results not-found">
                    	<header>
                        	<h2 class="entry-title"></h2>
                        </header>
                        <p>Przepraszamy, ale nic nie znaleziono</p>
                    </article>
            <?php
				//Koniec
				endif; 
			?>
        </section><!--#content-container-->
        
        <?php
    // Jeśli jest to strona wyników wyszukiwania
		if ( is_search() ) :
	?>
		<header class="page-header">
			<h1 class="page-title">
				Szukana fraza:<br />
				<span><?php the_search_query(); ?></span>
			</h1>
		</header>
	<?php endif;//( is_search() ) ?>
        
    	<?php /*get_sidebar();*/ ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>