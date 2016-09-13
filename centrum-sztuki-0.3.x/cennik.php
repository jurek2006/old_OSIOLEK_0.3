<?php
/*
Template Name: Cennik
Description: Obsługuje stronę cennika
*/

get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container column-12">
        <div class="cennik clearfix">
        <h1>Cennik biletów - Kino Odra</h1>
		<?php

				
				//pobranie slug aktulnie otwartego wydarzenia i wczytanie dla niego pods'a
				//$slug = pods_v('last','url');
				//get pods object
				$blad = false;
				$poz_cennika = array(
									'wt-czw-2d', 
									'wt-czw-3d', 
									'tania-sroda-2d', 
									'tania-sroda-3d',
									'weekend-swieta-2d',
									'weekend-swieta-3d',
									'filmowy-poranek-2d',
									'filmowy-poranek-3d',
									'dkf-kino-seniora-2d',
									'dkf-kino-seniora-3d' 
									);
				foreach($poz_cennika as $poz){
					//pobiera po kolei slugi elementów podsa cennik z tabeli $poz_cennika i przepisuje je do zmiennej cennik
					$pods = pods( 'cennik', $poz );
					if ($pods->exists()){
						$cennik[$poz] = array(
											'normalny' => $pods->display('normalny'),
											'ulgowy' => $pods->display('ulgowy'),
											'rodzinny' => $pods->display('rodzinny'),
											'grupowy' => $pods->display('grupowy'),
											);
						
					}//if ($pods->exists())
					else{
						$blad = true;
					}//else - if ($pods->exists())
				}//foreach($poz_cennika as $poz)
				
				if(!$blad){
					//jeśli bezbłędnie pobrano wszystkie potrzebne elementy pods
					//wyświetla dwie tabele z cennikiem
					//kwota(liczba) w funkcji wyswietlKwote dostaje standardowo span o klasie kwota-liczba
					?>
                    <div class="bilety2d">
                    	<h2>Bilety 2D</h2>
                        <table>
                            <colgroup>
                                <col class="etykiety">
                                <col>
                                <col>
                                <col>
                            </colgroup>
                            <tr>
                                <th></th>
                                <th>Wt, Czw</th>
                                <th>Tania Środa!</th>
                                <th>Pt-Nd, Święta</th>
                            </tr>
                            <tr>
                                <th>Normalny</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-2d']['normalny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-2d']['normalny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['weekend-swieta-2d']['normalny']); ?></td>
                            </tr>
                            <tr>
                                <th>Ulgowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-2d']['ulgowy']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-2d']['ulgowy']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-2d']['ulgowy']); ?></td>
                            </tr>
                            <tr>
                                <th>Rodzinny</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-2d']['rodzinny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-2d']['rodzinny']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-2d']['rodzinny']); ?></td>
                            </tr>
                            
                            <tr>
                                <th>Grupowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-2d']['grupowy']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-2d']['grupowy']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-2d']['grupowy']); ?></td>
                            </tr>
                            <tr>
                                <th>Filmowe poranki</th>
                                <td></td>
                                <td></td>
                                <td><?php echo wyswietlKwote( $cennik['filmowy-poranek-2d']['normalny']); ?></td>
                            </tr>
                             <tr>
                                <th>DKF, Kino Seniora</th>
                                <td><?php echo wyswietlKwote( $cennik['dkf-kino-seniora-2d']['normalny']); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            
                        </table>
                    </div><!--.bilety2d-->
                    
                    <div class="bilety3d">
                    	<h2>Bilety 3D</h2>
                        <table>
                            <colgroup>
                                <col class="etykiety">
                                <col>
                                <col>
                                <col>
                            </colgroup>
                            <tr>
                                <th class="do-ukrycia"></th>
                                <th>Wt, Czw</th>
                                <th>Tania Środa!</th>
                                <th>Pt-Nd, Święta</th>
                            </tr>
                            <tr>
                                <th class="do-ukrycia">Normalny</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-3d']['normalny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-3d']['normalny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['weekend-swieta-3d']['normalny']); ?></td>
                            </tr>
                            <tr>
                                <th class="do-ukrycia">Ulgowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-3d']['ulgowy']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-3d']['ulgowy']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-3d']['ulgowy']); ?></td>
                            </tr>
                            <tr>
                                <th class="do-ukrycia">Rodzinny</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-3d']['rodzinny']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-3d']['rodzinny']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-3d']['rodzinny']); ?></td>
                            </tr>
                            
                            <tr>
                                <th class="do-ukrycia">Grupowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt-czw-3d']['grupowy']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania-sroda-3d']['grupowy']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend-swieta-3d']['grupowy']); ?></td>
                            </tr>
                            <tr>
                                <th class="do-ukrycia">Filmowe poranki</th>
                                <td></td>
                                <td></td>
                                <td><?php echo wyswietlKwote( $cennik['filmowy-poranek-3d']['normalny']); ?></td>
                            </tr>
                             <tr>
                                <th class="do-ukrycia">DKF, Kino Seniora</th>
                                <td><?php echo wyswietlKwote( $cennik['dkf-kino-seniora-3d']['normalny']); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            
                        </table>
                        <p>+ dopłata 2 zł za okulary jednorazowe</p>
                        
                      </div><!--.bilety3d-->
                      </div><!--.cennik clearfix-->
                      
                        <ul class="cennik">                    
                            <li>Do ceny biletów na seanse 3D doliczana jest fakultatywna <strong>opłata w wysokości 2 zł za okulary 3D</strong>. Wg specyfikacji technicznej są one przeznaczone do użytku jednorazowego. Widz podejmuje samodzielną decyzję o ich ewentualnym ponownym wykorzystaniu. Prosimy o sprawdzenie zaraz po zakupie, czy okulary 3D nie są uszkodzone lub zarysowane. Okulary używane nie podlegają reklamacji.</li>
                                <li><strong>Tania środa</strong> uprawnia wszystkich do korzystania z promocyjnej ceny</li>
                                <li><strong>Poranki filmowe</strong> – seanse niedzielne dla dzieci do godz. 12:00</li>
                                <li><strong>Bilety ulgowe</strong> przysługują dzieciom, uczniom i studentom do 26 roku życia oraz emerytom i rencistom za okazaniem ważnej legitymacji</li>
                                <li><strong>Bilety grupowe</strong> przysługują wyłącznie grupom zorganizowanym, min. 15 osób (ze szkół, zakładów pracy, stowarzyszeń, fundacji) dotyczy wybranych seansów i obowiązuje tylko przy wcześniej telefonicznie zgłoszonej rezerwacji</li>
                                <li><strong>Bilety rodzinne</strong> przysługują min. 3-osobowym rodzinom, z co najmniej jednym dzieckiem do lat 12 i co najmniej jedną osobą dorosłą</li>
                                <li><strong>Oławska Karta Dużej Rodziny</strong> uprawnia do 20 procent zniżki na bilety normalne i ulgowe oraz seanse Poranek z wyłączeniem Taniej Środy. Ulga nie dotyczy sprzedaży okularów 3D.</li>
                                <li><strong>Bilet otwarty</strong> – idealna propozycja pracodawcy dla pracowników. Zakup BILETU OTWARTEGO do kina Odra prowadzonego przez Centrum Sztuki w Oławiena  na filmy 2D i 3D z terminem ważności maksymalnie 3 miesiące od daty nabycia. 
<p>BILET OTWARTY może nabyć firma lub osoba prywatna przy zakupie min. 50 sztuk biletów.</p>
<p>BILET OTWARTY honoruje się  przez cały tydzień w kinie ODRA w Oławie, zgodnie z wytycznymi cenowymi. Jeden BILET OTWARTY wymienia się w kasie kina na jeden bilet na wybrany film.</p> 
<p>BILET OTWARTY nie upoważnia do pierwszeństwa w obsłudze przy kasie, ani nie gwarantuje miejsca na sali, zachęcamy do wcześniejszej rezerwacji biletu na odpowiedni seans. </p>
<p>BILET OTWARTY nie może być opłacane kartami płatniczymi, kredytowymi czy też za pomocą kuponów handlowych. </p>
<p>BILET OTWARTY obowiązuje także na premiery i pokazy przedpremierowe, nie obowiązuje natomiast na pokazy specjalne (kino zastrzega sobie prawo do zdefiniowania co jest wydarzenie specjalnym) oraz maratony filmowe. </p>
<p>BILET OTWARTY nie podlega zwrotom.</p>
</li>
<li>Akceptujemy kupony <strong>CinemaProfit</strong> i <strong>QlturaProfit</strong> (dotyczy tylko oferty kinowej).</li>
                        </ul>
                        
 <!--Loga CinemaProfit i QlturaProfit-->  
 <img src="<?php echo get_stylesheet_directory_uri() ?>/img/cinema_profit_logo_300.jpg" alt="CinemaProfit" />  
 <img src="<?php echo get_stylesheet_directory_uri() ?>/img/logo_Qltura_300.jpg" alt="QlturaProfit" />                

					<?php
				}//if(!$blad)
				else{
					?><p>Błąd szablonu cennika CENNIK. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <a href="mailto:js@kultura.olawa.pl">js@kultura.olawa.pl</a>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p><?php
				}


		?>
		</section><!-- #content-container -->
        <?php //get_sidebar(); ?>
	</div><!-- #main-container -->       
</div><!--#main-wrap - koniec-->

<?php function wyswietlKwote($kwota, $jednostka='zł', $klasa_dla_liczby='kwota-liczba'){
			$kwota_rozbite = explode( ',', $kwota);
			return '<span class="'.$klasa_dla_liczby.'">'.$kwota_rozbite[0].'</span> '.$jednostka;
		}//function wyswietlKwote()
?>

<?php get_footer(); ?>