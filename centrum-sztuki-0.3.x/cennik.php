<?php
/*
Template Name: Cennik
Description: Obsługuje stronę cennika (korzystając z nowego pods cennik_ceny)
*/

get_header(); ?>
<div id="main-wrap">
	<div id="main-container" class="clearfix">
    
    	<section id="content-container column-12">
        <div class="cennik clearfix">
        <h1>Cennik biletów - Kino Odra</h1>
		<?php

				
				$blad = false;

				$poz_cennika = array(
									'wt_czw', 
									'tania_sroda', 
									'weekend_swieta',
									'poranki',
									'dkf', //kino seniora jest aktualnie pomijane przy wyświetlaniu tego cennika, bo jest jedną katagorią w nim, razem z DKF (gdyby były to różne ceny, to trzeba tutaj rozróżnić - dodać kino_seniora)
									);

				foreach($poz_cennika as $poz){
					//pobiera po kolei slugi elementów podsa cennik_ceny (nowy pods cen) z tabeli $poz_cennika i przepisuje je do zmiennej cennik
					$pods = pods( 'cennik_ceny', $poz );
					if ($pods->exists()){
						$cennik[$poz] = array(
											'normalny2d' => $pods->display('normalny2d'),
											'ulgowy2d' => $pods->display('ulgowy2d'),
											'rodzinny2d' => $pods->display('rodzinny2d'),
											'grupowy2d' => $pods->display('grupowy2d'),
                                            'normalny3d' => $pods->display('normalny3d'),
                                            'ulgowy3d' => $pods->display('ulgowy3d'),
                                            'rodzinny3d' => $pods->display('rodzinny3d'),
                                            'grupowy3d' => $pods->display('grupowy3d'),
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
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['normalny2d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['normalny2d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['weekend_swieta']['normalny2d']); ?></td>
                            </tr>
                            <tr>
                                <th>Ulgowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['ulgowy2d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['ulgowy2d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['ulgowy2d']); ?></td>
                            </tr>
                            <tr>
                                <th>Rodzinny</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['rodzinny2d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['rodzinny2d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['rodzinny2d']); ?></td>
                            </tr>
                            
                            <tr>
                                <th>Grupowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['grupowy2d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['grupowy2d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['grupowy2d']); ?></td>
                            </tr>
                            <tr>
                                <th>Filmowe poranki</th>
                                <td></td>
                                <td></td>
                                <td><?php echo wyswietlKwote( $cennik['poranki']['normalny2d']); ?></td>
                            </tr>
                             <tr>
                                <th>DKF, Kino Seniora</th>
                                <td><?php echo wyswietlKwote( $cennik['dkf']['normalny2d']); ?></td>
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
                                <th>Normalny</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['normalny3d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['normalny3d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['weekend_swieta']['normalny3d']); ?></td>
                            </tr>
                            <tr>
                                <th>Ulgowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['ulgowy3d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['ulgowy3d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['ulgowy3d']); ?></td>
                            </tr>
                            <tr>
                                <th>Rodzinny</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['rodzinny3d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['rodzinny3d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['rodzinny3d']); ?></td>
                            </tr>
                            
                            <tr>
                                <th>Grupowy</th>
                                <td><?php echo wyswietlKwote($cennik['wt_czw']['grupowy3d']); ?></td>
                                <td><?php echo wyswietlKwote($cennik['tania_sroda']['grupowy3d']); ?></td>
                                <td><?php echo wyswietlKwote( $cennik['weekend_swieta']['grupowy3d']); ?></td>
                            </tr>
                            <tr>
                                <th>Filmowe poranki</th>
                                <td></td>
                                <td></td>
                                <td><?php echo wyswietlKwote( $cennik['poranki']['normalny3d']); ?></td>
                            </tr>
                             <tr>
                                <th>DKF, Kino Seniora</th>
                                <td><?php echo wyswietlKwote( $cennik['dkf']['normalny3d']); ?></td>
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
                                <li><strong>Bilety ulgowe</strong> przysługują uczniom i studentom do 26. roku życia, a także rencistom, emerytom, posiadaczom Oławskiej Karty Seniora oraz osobom niepełnosprawnym. Bilety ulgowe nie przysługują opiekunom osób niepełnosprawnych. Bilety ulgowe są honorowane za okazaniem ważnej legitymacji uprawniającej do korzystania z nich.
Posiadaczom Oławskiej Karty Dużej Rodziny przysługuje 20% zniżki na bilety normalne i ulgowe odnośnie wszystkich wydarzeń kulturalnych i seansów filmowych. Zniżka nie dotyczy promocyjnych seansów Taniej Środy. Ulga nie dotyczy sprzedaży okularów 3D.</li>
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
                        </ul>
                                      

					<?php
				}//if(!$blad)
				else{
					?><p>Błąd szablonu cennika CENNIK. Jeśli widzisz ten komunikat skontaktuj się z nami na adres <span class="mail">js(małpka)kultura.olawa.pl</span>. Dziękujemy za pomoc w ulepszaniu naszej strony.</p><?php
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