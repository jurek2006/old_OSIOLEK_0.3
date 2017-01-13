<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
    <title></title>
    
    <!--***** META TAG FOR DO NOT LIE-->
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
    
    <?php 			
	
		//Wywołanie funkcji wp_head() powinno znajdować się przed znacznikiem zamykającym nagłówek
		wp_head(); 
	?>
    <!--Czcionka MAGRA-->
    <link href='http://fonts.googleapis.com/css?family=Magra:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />

	<!-- styl tylko dla wersji ekran -->
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ekran.css" />
    
	
</head>


<body> 

    