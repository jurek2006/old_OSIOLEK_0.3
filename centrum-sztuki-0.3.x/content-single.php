                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                	<header>
                    	<h1 class="entry-title">
                        	<?php the_title(); ?>
                        </h1>
                        <?php if( is_single() ): ?>
                            <p>
                                Opublikowano dnia <time datetime="<?php echo get_the_date(); ?>"><?php the_time(); ?>,</time>
                                autor: <?php the_author_link(); ?>
                                <?php
                                    //Czy komentarze są otwarte?
                                    if( comments_open() ) : ?>
                                    &bull; <?php comments_popup_link('Brak komentarzy', '1 komentarz', 'komentarzy: %');
                                    endif; 
									
									//Wyświetlenie kategorii i tagów w pojedynczych wpisach
									if( is_singular('post') ):
									?>
                                    	<br />Kategorie: <?php the_category(', '); ?>
                                        <?php the_tags(' Tagi: ', ', ', ''); ?>
                            </p>
                        <?php
									endif;//( is_singular('post') )
							endif; //(is_single()) 
						?>
                    </header>
                    <?php
						//Treść
						the_content();
					?>
                </article>
 