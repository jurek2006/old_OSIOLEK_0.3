﻿
    
<!--Zamknięcie DIVów przeniesione do plików szablonowych jak index.php, wydarzenia.php, wydarzenia-single.php, adresy.php--> 
    
    <!-- STOPKA -->
    
    <div id="footer-wrap">
    	<footer class="clearfix">
                <?php  
					//dolne menu nawigacyjne
					wp_nav_menu( array(
						'theme_location' => 'bottom-navigation',
						'container' => 'nav')
					);
					
				?>

            <div id="kontakt" class="clearfix">
                <h1>Centrum Sztuki w Oławie</h1>
                <div class="fullWidth">
                    <h2>Kasa biletowa - telefon: 71 735 15 70</h2>
                    <p>ul. Młyńska 3, 55-200 Oława, <span class="mail">bilety(małpka)kultura.olawa.pl</span></p>
                </div>
                <div>
                    <h2>OWE Odra - Kino Odra</h2>
                    <p>ul. Młyńska 3, 55-200 Oława</p> 
                    <p>tel. (biura) 71 735 15 75 , fax. 71 735 19 58</p> 
                    <p><span class="mail">sekretariat(małpka)kultura.olawa.pl</span></p>
                </div>
                <div>         	
                    <h2>Ośrodek Kultury</h2>
                    <p>ul. 11 Listopada 27, 55 - 200 Oława</p>
                    <p>tel. 71 313 28 29 / 71 313 33 65</p>
    
                </div>
            </div><!--#kontakt-->
            <a class="antyspam" href="http://nie-spamuj.eu/email.php">Antyspam</a>
        </footer>
    </div><!--#footer-wrap-->
    


<?php  
	//Zamknięcie WordPressa przed zamknięciem elementu body
	wp_footer();
?>





</body>
</html>