<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
    <title>
    <?php  
    	$dopisek='';
		//Tytuł - ustawiany na podstawie tego, co jest otwarte, tutaj dodawany jest początek do tytułu
		if (96 == $post->post_parent) {
		//jeśli jest to dowolna podstrona wydarzeń (a właściwie kategorii wydarzeń)
			$dopisek = 'Wydarzenia - ';
		}
		else if(get_the_ID()==237){
		//jeśli są to wszystkie zajęcia - podstrona z Edukacja
			$dopisek = 'Edukacja - ';
		}
		else if(497 == $post->post_parent) {
		//jeśli jest to podstrona z Edukacja - poza "wszystkie zajęcia"
			$dopisek = 'Edukacja - zajęcia ';
		}
		
		if ( is_home () ) {
		//strona główna 
            bloginfo( 'name' ); 
        } elseif ( is_archive() ) {
		//archiwum
            single_cat_title(); echo ' &bull; ' ; bloginfo( 'name' ); 
        } elseif ( is_singular() ) { 
		//pojedynczy wpis
            echo $dopisek; single_post_title(); echo ' &bull; ' ; bloginfo( 'name' ); 
        } else { 
		//pozostałe, czyli strona
            wp_title( '', true ); echo ' &bull; ' ; bloginfo( 'name' ); 
        }

	?>
    </title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
	<!--Wczytanie styli CSS odbywa się w functions za pomocą mechanizmu WordPress-->
    
    <link rel="pingback" href="<?php bloginfo('pingbck_url'); ?>" />
    
    <!--***** META TAG FOR DO NOT LIE-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!--***** SCRIPT TO HELP OLD IE UNDERSTAND HTML5 AND MEDIA QUERIES-->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->
  

    
    <?php 
		// Dodanie do kolejki kodu JavaScript dla zakorzenionych komentarzy, jeśli są włączone
        if ( is_singular() && get_option( 'thread_comments' ) )
            wp_enqueue_script( 'comment-reply' );
			
	
		//Wywołanie funkcji wp_head() powinno znajdować się przed znacznikiem zamykającym nagłówek
		wp_head(); 
	?>
    <!--Czcionka MAGRA-->
    <link href='http://fonts.googleapis.com/css?family=Magra:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
    
	
</head>
<?php 
//Jeśli jest to przeglądarka mobilna to do klas elementu body dodawana jest klasa 'is_mobile'
$klasa_mobile = NULL;
if (wp_is_mobile() ) {
	$klasa_mobile = 'is_mobile';
}//if (wp_is_mobile() )?>

<body <?php body_class($klasa_mobile); ?>> 

    <!-- Skrypt dodający klasę sticky do div#header-wrap - wyłączony jeśli przeglądarka zgłasza się jako mobilna -->
    <?php if (!wp_is_mobile() ){
			printf('<script>   
						jQuery(window).scroll(function() {
							if (jQuery(this).scrollTop() > 1){  
								jQuery(\'div#header-wrap\').addClass("sticky");
								
							}
							else{
								jQuery(\'div#header-wrap\').removeClass("sticky");
							}
						});
					</script>');
		}//if (!wp_is_mobile() )
    ?>

<!-- BANNER - początek -->

<!-- <div id="banner-wrap">
	<a class="banner" href="http://www.forumwokolkina.kultura.olawa.pl" target="_blank">
	<?php //USUNĄĆ KOMMMENT echo '<img src="'.get_stylesheet_directory_uri().'/img/banner-fwk-kultura.jpg" />'; ?>
	</a>
</div>  -->

<!-- BANNER - koniec -->

<!-- BANNER - nowy numer telefonu -->
<div id="banner-wrap">
	<div class="bannerHtml">
		<img src="<?php echo get_template_directory_uri() ?>/style/tel100px.jpg"/>
		<div>
			<p>Kasa biletowa: </p>
			<a class="numer" href="tel:+48717351570"> 71 735 15 70</a>
		</div>
	</div>
</div>
<!-- BANNER - koniec - nowy numer telefonu -->

<div id="header-wrap">
        	<header class="clearfix">
            	<a href="<?php echo home_url() ?>"><img src="<?php echo get_template_directory_uri() ?>/style/logo_pegaz_header.png" alt="Centrum Sztuki w Oławie" /></a> 
               
                
                <?php 
					 
					//Górne menu nawigacyjne
					
					if (current_user_can( UPR_MENU_USER )){
					//jeśli jest zalogowany użytkownik o uprawnieniach do publikacji postów (co najmniej Autor) to wyświetlane jest specjalne
					//menu główne
						wp_nav_menu( array(
							'theme_location' => 'top-user-navigation',
							'container' => 'nav',
							'menu_class' => 'primary-nav')
						);
					}
					else{
					//jeśli nie jest zalogowany użytkownik o uprawnieniach do publikacji postów - wyświetlane jest standardowe menu główne
						wp_nav_menu( array(
							'theme_location' => 'top-navigation',
							'container' => 'nav',
							'menu_class' => 'primary-nav')
						);
					}
					
					
				?>
                <!--BUTTON UŻYWANY W MOBILE MENU <button class="nav-button">Toggle Button</button>-->
            </header>
</div><!-- #header-wrap-->


<!--DIVY przeniesione do plików szablonowych jak index.php, wydarzenia.php, wydarzenia-single.php, adresy.php-->          
<!--<div id="main-wrap">
	<div id="main-container" class="clearfix">-->