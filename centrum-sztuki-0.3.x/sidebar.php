<aside>
	<ul id="sidebar" class="clearfix">
    <?php  
			//zczytanie nazwy kategorii głównej dla otwartej strony - na jej podstawie wczytywane jest odpowiedni widżet
			//podstrona_drugi_poziom określa drugi poziom zagnieżdżenia, 
			//np. dla www.kulturaolawa.nazwa.pl/testy_laboratorium/kino/zarzadzanie/ jest to zarządzanie 
			//(tak samo dla www.kultura.olawa.pl/kino/zarzadzanie/)
			$podstrona = pods_v(0,'url');
			$podstrona_drugi_poziom = pods_v(1,'url');
			if($podstrona=='testy_laboratorium'){
			//jeśli nie jest to uruchomienie ze strony kultura.olawa.pl tylko z testy_laboraratorium
			//to musi być wzięty jako podstrona kolejny człon nazwy
			//w ten sposób szablon jest uniwersalny dla tych dwóch wersji uruchomienia strony
				$podstrona = pods_v(1,'url');
				$podstrona_drugi_poziom = pods_v(2,'url');
			}//if($podstrona=='testy_laboratorium')
			
			//jeśli $podstrona jest pusta (strona główna - wydarzenia) lub należy do kategorie-wydarzen (również lista wydarzeń) lub kategorie-edukacji (lista kursów) lub do archiwum wydarzeń
			 // to jest wczytywany obszar na widżety pasek-boczny-glowny
	
			$tablica = array('kategorie_wydarzen', 'kategorie-edukacji', 'archiwum-wydarzen');
			if (empty($podstrona) || in_array( $podstrona, $tablica )){

					$podstrona = 'pasek-boczny-glowny';
			}
			else if($podstrona == 'ogloszenia')	{
			//jeśli jest to podstrona ogloszenia to podmieniana jest na o-centrum żeby wczytywany był widżet z menu o-centrum
				$podstrona = 'o-centrum';
			}
			else if($podstrona == 'kino'){
				//jeśli podstrona to kina, a podstrona drugiego poziomu kino-zarzadzanie (ścieżka: /kino/zarzadzanie)
				//to wczytywany do sidebaru jest obszar na widżety kino-zarzadzanie
				if($podstrona_drugi_poziom == 'zarzadzanie'){
					$podstrona = 'kino-zarzadzanie';
				}
			}
			
			
			if( !dynamic_sidebar($podstrona)) :
	?>
    			<li>Umieść jakieś widżety w obszarze widżetów w prawej kolumnie!</li>
    <?php 	endif; ?>
    </ul>

</aside><!--#sidebar-container - koniec-->