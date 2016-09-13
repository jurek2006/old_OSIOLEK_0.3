<?php
/*
Template Name: Adresy
Description: Obsługuje strony adresów (np. Ośrodek Kultury, Centrum Sztuki) na podstawie pods Adresy

*/

get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">

    	<section id="content-container" class="column-9">
		<?php
				
				//pobranie slug aktulnie otwartego wydarzenia i wczytanie dla niego pods'a
				$slug = pods_v('last','url');
				//get pods object
				$pods = pods( 'adresy', $slug );
				
				//Put field values into variables
				$title = $pods->display('name');
				$tresc = $pods->display('tresc_strony');
				//pobieranie koloru tła na podstawie lokalizacji
				//$kolor_tla_naPodstLokalizacji = $pods->field('lokalizacje.adres.kolor');
				//$kolor_tla_naPodstLokalizacji = $kolor_tla_naPodstLokalizacji[0];
		?>
        <h1><?php echo $title; ?></h1>
        
        <?php echo $tresc ?>

		</section><!-- #content-container -->

        <?php /*get_sidebar();*/ ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php get_footer(); ?>