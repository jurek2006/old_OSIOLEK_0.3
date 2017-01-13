<?php

	//SETTINGS
	
	//PRZESUNIECIE_CZASU określa różnicę pomiędzy czasem serwera a czasem rzeczywistym
	//np. serwer ma czas 8:30 a jest rzeczywiście 10:30 więc stała ma wartość "+2 hour"
	//(stała używana jest w funkcji pobierzDateTeraz() wywoływanej przy każdym pobieraniu aktualnego czasu
	define("PRZESUNIECIE_CZASU", "+1 hour");

	// ADRES SYSTEMU BILETOWEGO VISUALTICKET DLA CSO
	define("ADRES_VISUALTICKET", "http://s7.systembiletowy.pl/cso/");
	
	//koniec SETTINGS

	//-----------FUNKCJE DODANE W 0.3.3.2

	//---------------------------------------------------------------------------------------------------------
	//stała określa, jakie uprawnienia musi mieć użytkownik do strony importu projekcji
	//(używane przy wyświetlaniu treści strony w szablonie ale także do dołączania skryptu .js)
	define("UPR_IMPORT_PROJEKCJI", "publish_posts");
	
	//stała określająca uprawnienia potrzebne do dostępu do strony kino-zarządzanie (kino-zarzadzanie.php)
	define("UPR_KINO_ZARZADZANIE", "publish_posts"); 
	
	//uprawnienia potrzebne do wyświetlania menu top-user-navigation zamiast top-navigation
	define("UPR_MENU_USER", "publish_posts"); 


	$tlumaczenieStatusuPostow = Array("publish" => "Opublikowane", "draft" => "Szkic", "pending" => "Oczekuje na przeglad", "future" => "Zaplanowana publikacja");
	
	//jQuery UI
	function add_jquery_ui() {
		wp_enqueue_script( 'jquery-ui-core' );
		//wp_enqueue_script( 'jquery-ui-widget' );
//		wp_enqueue_script( 'jquery-ui-mouse' );
//		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
//		wp_enqueue_script( 'jquery-ui-slider' );
//		wp_enqueue_script( 'jquery-ui-tabs' );
//		wp_enqueue_script( 'jquery-ui-sortable' );
//		wp_enqueue_script( 'jquery-ui-draggable' );
//		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
//		wp_enqueue_script( 'jquery-ui-resize' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		//wp_enqueue_script( 'jquery-ui-button' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
	}
	add_action( 'wp_enqueue_scripts', 'add_jquery_ui' );
	
	//dodanie plików stylu jQueryUI z serwerów Google
	function jquery_ui_enqueue_style(){
		wp_enqueue_style(	'plugin_name-admin-ui-css',
						'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/ui-lightness/jquery-ui.css',
						false,
						'PLUGIN_VERSION',
						false);
	}
	add_action( 'wp_enqueue_scripts', 'jquery_ui_enqueue_style' );

	
	//dołączenie skryptu visualticket_import.js jeśli uzytkownik ma uprawnienia na poziomie
	//wystarczającym do wyświetlenia strony Import projekcji i jeśli jest na tej stronie
	//a więc szablon aktualnej strony to 
	function add_my_script() {
		//spolszczenie datepicker z jQueryUI
		//wp_register_script('datepicker_pl', get_stylesheet_directory_uri().'/visualticket_import/datepicker-pl.js', array( 'jquery' ));

		//mój skrypt importu terminów-projekcji z VisualTicket
		wp_register_script('visualticket_import', get_stylesheet_directory_uri().'/visualticket_import/visualticket_import.js', array( 'jquery' ));
		if (current_user_can( UPR_IMPORT_PROJEKCJI ) && is_page_template('import-projekcji.php')){
			//wp_enqueue_script('datepicker_pl');
			wp_enqueue_script('visualticket_import');
		}
	}  
	add_action( 'wp_enqueue_scripts', 'add_my_script' );

	function my_enqueue($hook) {
	// dodanie skryptu w menu admin, jeśli jest to strona post-new.php lub post.php (czyli strona dodawania noweg lub edycji dowolnego typu postu)
	    if (( 'post-new.php' != $hook )&&( 'post.php' != $hook )) {
	        return;
	    }

	    wp_enqueue_script( 'my_custom_script', get_stylesheet_directory_uri(). '/admin_js/post_dodawanie_edycja.js' );
	}
	add_action( 'admin_enqueue_scripts', 'my_enqueue' );

	function dodajPaginacje($pods){
	// Funkcja dodająca zaawansowaną paginację pods z etykietami w j.pol.
	// parametr $pods to pod dla którego tworzona jest paginacja

        echo $pods->pagination( array(  'type' => 'advanced',
                                        'first_text' => '&laquo;Pierwsza strona',
                                        'prev_text' => '&lsaquo;Poprzednia',
                                        'next_text' => 'Następna&rsaquo;',
                                        'last_text' => 'Ostatnia strona&raquo;'
                                        ) );
	}

	//--------------------------------------
	
	// wczytanie standardowych stylów CSS - wczytywanych na początku działania strony
	function centrumSztuki_enqueue_style() {
		wp_enqueue_style( 'reset', get_template_directory_uri().'/style/reset.css' );
		wp_enqueue_style( 'medium768', get_template_directory_uri().'/style/medium768.css' ); 
		wp_enqueue_style( 'desktop', get_template_directory_uri().'/style/desktop.css' ); 
	}
	add_action( 'wp_enqueue_scripts', 'centrumSztuki_enqueue_style' );
 
	//Ustawienie szerokości treści w motywie
	if( !isset($content_width) )
		$content_width = 500;
		
	//Pozbycie się wpisu o wersji WordPressa <meta name="generator" content="WordPress 4.2.2" />
	remove_action('wp_head', 'wp_generator');
	
	//Konfiguracja motywu
	add_action('after_setup_theme', 'simpleblog_themesetup');
	
	

	
	function simpleblog_themesetup()
	{
		//Automatyczne linki kanałów RSS
		add_theme_support('automatic-feed-links');
		
		//Dodanie funkcji menu nawigacyjnych do zaczepu init
		add_action('init', 'simpleblog_register_menus');
		
		//Dodanie funkcji pasków bocznych do zaczepu widgets_init
		add_action('widgets_init', 'simpleblog_register_sidebars');
		
		//Dodanie do kolejki plików JavaScript w zaczepie wp_enqueue_scripts
		add_action('wp_enqueue_scripts','simpleblog_load_scripts');
		
		//Dodanie własnego rozmiaru obrazków
		add_image_size( 'projekcja-thumb', 0, 150 ); // rozmiar obrazka (miniatury) wyświetlany na liście kino - repertuar
		add_image_size( 'film-zapowiedz', 125, 185, array( 'center', 'top' ) ); //rozmiar okładki filmu do wyświetlania na pasku zapowiedzi
		//add_image_size( 'sidebar-full-width', 250, 0); //rozmiar obrazka wypełniający sidebar na całą szerokość (w szablonie założona 250px)
		//add_image_size( 'glowny-tresc', 1000 ); //Główny rozmiar obrazka w treści (o szerokości 1000px)
		
	}//simpleblog_themesetup
	
	

	//Rejestracja menu
	function simpleblog_register_menus()
	{
		register_nav_menus(
			array(
				'top-navigation' => 'Top navigation',
				'top-user-navigation' => 'Top user navigation',
				'bottom-navigation' => 'Bottom navigation',
				'category-navigation' => 'Nawigacja zmiany kategorii wydarzeń',
				'kursy-category-navigation' => 'Nawigacja zmiany kategorii kursów'
			)
		);
	}//simpleblog_register_menus
	
	//Zarejestrowanie obszarów widżetów
	function simpleblog_register_sidebars()
	{
		//Obszar widżetów w prawej kolumnie
		
		register_sidebar(	array(
								'name' => 'Outw',
								'id' => 'outw',
								'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
								'after_widget' => '</li>',
								'before_title' => '<h3 class="outw-widget-title">',
								'after-title' => '</h3>'
							)
		);
		
		register_sidebar(	array(
								'name' => 'O Centrum',
								'id' => 'o-centrum',
								'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
								'after_widget' => '</li>',
								'before_title' => '<h3 class="o-centrum-widget-title">',
								'after-title' => '</h3>'
							)
		);
		
		register_sidebar(	array(
								'name' => 'Pasek boczny główny',
								'id' => 'pasek-boczny-glowny',
								'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
								'after_widget' => '</li>',
								'before_title' => '<h3 class="pasek-boczny-glowny-widget-title">',
								'after-title' => '</h3>'
							)
		);
		
		register_sidebar(	array(
								'name' => 'Kino',
								'id' => 'kino',
								'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
								'after_widget' => '</li>',
								'before_title' => '<h3 class="kino-widget-title">',
								'after-title' => '</h3>'
							)
		);
		
		register_sidebar(	array(
								'name' => 'Kino zarządzanie',
								'id' => 'kino-zarzadzanie',
								'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
								'after_widget' => '</li>',
								'before_title' => '<h3 class="pasek-boczny-glowny-widget-title">',
								'after-title' => '</h3>'
							)
		);
	}//simpleblog_register_sidebars

	
	// Dodatek ułatwiający skalowanie obrazków wklejonych w edytorze (tylko pełny rozmiar obrazków) - modyfikacja własna
	//Jeśli wklejany obrazek ma klasę size-full lub glowny-tresc to funkcja "obcina" mu parametry width i height, żeby się dobrze skalował za pomocą CSS 
	//add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
	add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
	 
	function remove_width_attribute( $html ) {
		
		if(strpos ( $html , 'size-full') || strpos ( $html , 'glowny-tresc')){
			$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
		}
		return $html;
}
	
	//Kolejkowanie skryptów JavaScript
	function simpleblog_load_scripts()
	{
		//Kolejkowaie JavaScropt dla komentarzy dzielonych na wątki, jeśli funkcja ta zostanie włączona
		if( is_singular() && get_option('thread-comments') && comments_open() )
			wp_enqueue_script('comment-reply');
	}//simpleblog_load_scripts
	
	//---------------funkcje testowe - do usunięcia

	function consoleLog($tresc){
	// funkcja wyświetlająca $tresc w konsoli JS
		echo '<script>console.log("'.$tresc.'")</script>';
	}
	
	function returnZawartoscTabeli($tabela, $separator=', '){
		//zwraca string - zawartośc kolejnych elementów tablicy, jeśli element jest tablicą wyświetla zamiast niego 'Array'
		$wynik = '';
		if (count($tabela)>0){
			//return 'k: '.count($tabela);
			for($i=0; $i<count($tabela)-1; $i++){
				if(!is_array($tabela[$i])){
				$wynik .= $tabela[$i].$separator;
				}
				else
					$wynik .= 'Array'.$separator;
			}
			if(!is_array($tabela[count($tabela)-1])){
				$wynik .= $tabela[count($tabela)-1];
			}
			else
				$wynik .= 'Array';
		}
		return $wynik;
	}//returnZawartoscTabeli
	
	//---------------funkcje używane przy wyświetlaniu wydarzeń
	function pobierzTerminWydarzenia($data_i_godzina_wydarzenia, $dzien_rozpoczecia, $dzien_zakonczenia, $termin_opisowy, $alias_wernisazu, $czyWystawaZwernisazem=0)
 //Funkcja zwraca linię tekstową terminu wydarzenia w zależności pod parametrów
 //Zwraca NULL jeśli nie podano $data_i_godzina_wydarzenia (co jest błędem, bo w pods wydarzenia jest to wymagane)
 {
	 if(!empty($termin_opisowy)){
	 //jeśli wpisano termin w formie opisu to jest on najważniejszy i zwracany natychmiast
	 //w przeciwnym razie funkcja kontynuuje działanie
		 return $termin_opisowy;
	 }
	 
	 if(!empty($dzien_zakonczenia)){
	 //jeśli podano $dzien_zakonczenia to jest to typ daty od...do lub od...do z wernisażem
		 if(empty($dzien_rozpoczecia)){
		 //jeśli nie podano dnia rozpoczęcia, to jest to błąd (przy podawaniu zakończenia zawsze jest to termin od..do)
		 //zwracany jest komunikat 'Błąd daty rozpoczęcia'
		 	return 'Błąd daty rozpoczęcia';
		 	//$dzien_rozpoczecia = '0001-01-01';
		 }
		 
		 if($czyWystawaZwernisazem){
		 //jeśli wypełnione są pola 'dzień zakończenia' i 'dzień rozpoczęcia' to sprawdzane jest jeszcze $czyWystawaZwernisazem
		 //jeśli tak jest to do zwracanego wyjścia dodawany jest fragment ' Wernisaż ....' na podstawie $data_i_godzina_wydarzenia
			return 'Od '.zamienDateGodzinePodsNaTekst($dzien_rozpoczecia).' do '.zamienDateGodzinePodsNaTekst($dzien_zakonczenia).'. Wernisaż '.$data_i_godzina_wydarzenia;
		 }
		 else{
	     //jeśli nie jest to typ od...do z wernisażem tylko zwykły od...do
			 return 'Od '.zamienDateGodzinePodsNaTekst($dzien_rozpoczecia).' do '.zamienDateGodzinePodsNaTekst($dzien_zakonczenia);
		 }
		
	 }
	 if(!empty($dzien_rozpoczecia)){
	 //jeśli podany jest tylko dzień rozpoczęcia to jest to traktowane jako wydarzenie jednodniowe
			return zamienDateGodzinePodsNaTekst($dzien_rozpoczecia);
	 }
	 if(!empty($data_i_godzina_wydarzenia)){
	 //jeśli jest wypełnione pole 'data i godzina wydarzenia' to jest ono terminem
		return zamienDateGodzinePodsNaTekst($data_i_godzina_wydarzenia);
	 }
	 else{
	 //jeśli nie podano $data_i_godzina_wydarzenia - co jest błędem - zwraca NULL
	 }
	 
	 
	 	/*if($czyWystawaZwernisazem && !empty($data_i_godzina_wydarzenia)){
			if((!empty($dzien_zakonczenia))&&(!empty($dzien_rozpoczecia))){
				return 'Od '.zamienDateGodzinePodsNaTekst($dzien_rozpoczecia).' do '.zamienDateGodzinePodsNaTekst($dzien_zakonczenia).'. Wernisaż '.zamienDateGodzinePodsNaTekst($data_i_godzina_wydarzenia);
			}
			else
			{
				return $termin_opisowy.'. Wernisaż '.zamienDateGodzinePodsNaTekst($data_i_godzina_wydarzenia);
			}
		}
 
		if(!empty($data_i_godzina_wydarzenia)){
		//jeśli jest wypełnione pole 'data i godzina wydarzenia' to jest ono terminem
			return zamienDateGodzinePodsNaTekst($data_i_godzina_wydarzenia);
		}
		else if((!empty($dzien_zakonczenia))&&(!empty($dzien_rozpoczecia))){
		//jeśli wypełnione są pola 'dzień zakończenia' i 'dzień rozpoczęcia'
			return 'Od '.zamienDateGodzinePodsNaTekst($dzien_rozpoczecia).' do '.zamienDateGodzinePodsNaTekst($dzien_zakonczenia);
		}
		else if(!empty($dzien_rozpoczecia)){
		//jeśli podany jest tylko dzień rozpoczęcia to jest to traktowane jako wydarzenie jednodniowe
			return zamienDateGodzinePodsNaTekst($dzien_rozpoczecia);
		}
		else{
		//jeśli nie wypełnione jest żadne pole to jako termin przyjmowana jest wartość pola 'termin opisowy'
			return $termin_opisowy;
		}*/

 }
 
//FUNKCJA POBIERAJĄCA DATĘ "TERAZ"

function pobierzDateTeraz(){
	$teraz = new DateTime(date("Y-m-d H:i"));
	$teraz->modify(PRZESUNIECIE_CZASU);
	return $teraz;
}
 
//FUNKCJE ZAMIENIAJĄCE FORMAT DATY PODS NA NP. 27 czerwca 2014 o godz. 17:00----------------------------------------------------
 
 //zawiera listę miesięcy (0 stycznia, 1 lutego!!! itd.)
$miesiaceOdmienione = array('stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia');

//zawiera dni tygodnia 0 - niedziela, 6 - sobota - zgodnie z formatem dla funkcji date() 'w'
$dniTygodnia = array('Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota');

//zawiera dni tygodnia odmienione 0 - niedzieli, 6 - soboty - zgodnie z formatem dla funkcji date() 'w'
$dniTygodniaOdmienione = array('niedzieli', 'poniedziałku', 'wtorku', 'środy', 'czwartku', 'piątku', 'soboty');

function walidujDate($date, $format = 'Y-m-d')
//funkcja walidująca datę - sprawdza czy $date jest w zadanym formacie (domyślnie YYYY-MM-DD czyli Y-m-d)
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}//function walidujDate($date, $format = 'Y-m-d')
 
 function zamienDateGodzinePodsNaTekst($data_godzina, $lacznik=' o godz. ', $bez_roku=FALSE)
//zamienia datę albo w formacie (RRRR-MM-DD) na DD miesiąc RRRR albo datę i godzinę (RRRR-MM-DD HH:MM) na DD miesiąc RRRR o godz. HH:MM
//np 2014-11-15 -> 15 listopada 2014, 2014-11-15 18:30 -> 15 listopada 2014 o godz. 18:30
//$lacznik to tekst rozdzielający datę i godzinę na wyjściu, np dla $lacznik=='o godzinie' wynik jest listopada 2014 o godz. 18:30
//domyślny $lacznik to 'o godz.'
//$bez_roku == TRUE powoduje, że data zostanie zwrócona bez roku
//jeśli z jakiegoś powodu funkcja nie może dokonać zamiany zwraca NULL
{
	
	 if(strlen($data_godzina)==16){
	 //jeśli string ma długość 16 znaków, najprawdopodobniej jest to RRRR-MM-DD HH:MM
	 	//rozdzielenie $data_godzina względem spacji
	 	if (count($data_godzina = explode(' ',$data_godzina))==2){
		//jeśli $data_godzina ma dwie części po rozdzieleniu to jest szansa, że jest to data godzina w formacie RRRR-MM-DD HH:MM
			$data = $data_godzina[0];
			$godzina = $data_godzina[1];
		}
		else{
		//jeśli $data_godzina ma inną ilość elementów niż 2 - nie jest to prawidłowy format RRRR-MM-DD HH:MM
		//funkcja zwraca NULL
			//return 'Data godzina ma '.count($data_godzina).' elementów zamiast 2';
			return NULL;
		}
	 }
	 else if (strlen($data_godzina)==10){
	 //jeśli string ma długość 10 znaków, najprawdopodobniej jest to RRRR-MM-DD
	 	$data = $data_godzina;
	 }
	 else{
	 //jeśli nie jest to ciąg o długości ani 10 ani 16 znaków to na pewno nie jest to format daty ani daty i godziny pods
	 //zwraca wtedy NULL
	 	//return 'Ciąg daty&godziny nieodpowiedniej długości';
	 	return NULL;
	 }
	 
	 //jeśli na tym etapie funkcja nadal działa to oznacza, że istnieje zmienna $data którą trzeba spróbować zamienić na tekst
	 //$data może pochodzić zarówno z danej wejściowej RRRR-MM-DD jak i RRRR-MM-DD HH:MM

	$data_zamieniona = zamienDateNaTekst($data, $bez_roku);
	
	if(is_null($data_zamieniona)){
	//jeżeli funkcja zamienDateNaTekst zwróciła NULL (nie udało się przekształcić daty) - funkcja zwraca NULL
		//return 'zamienDateNaTekst zwróciła NULL';
		return NULL;
	}
	
	//jeśli funkcja działa na tym etapie to istnieje $data_zamieniona (o wart. np 27 listopada 2014)
	//trzeba sprawdzić, czy należy jeszcze dodać do niej godzinę
	if(!isset($godzina)){
	//jeśli nie jest ustawiona funkcja $godzina oznacza to, że format wejściowy był RRRR-MM-DD
	//zwraca wtedy tylko zamienioną datę jako wynik funkcji
		return $data_zamieniona;
	}
	else
	//jeśli istnieje $godzina to jest szansa, że jest to element formatu RRRR-MM-DD HH:MM
	{
		//sprawdzenie czy $godzina jest zgodna z HH:MM
		if (count($godzina_tab = explode(':',$godzina))==2){
		//$godzina ma dwie części po rozdzieleniu względem:
			 
			 $hh = $godzina_tab[0];
			 $mm = $godzina_tab[1];
			 
			 if(!is_numeric($hh) ||!is_numeric($mm))
			 //jeśli godzina lub minuta nie jest liczbą funkcja zwraca NULL
			 {
				 //return 'Godzina lub minuta nie jest liczbą';
				 return NULL;
			 }
			 
			 settype($hh, 'integer');
			 if(($hh < 0) || ($hh > 23)){
			 //jeśli numer godziny nie mieści się w przedziale 0-23 nie jest to godzina - funkcja zwraca NULL
			 	//return 'Nieprawidłowy nr godziny';
			 	return NULL;
			 }
			 
			 settype($mm, 'integer');
			 if(($mm < 0) || ($mm > 59)){
			 //jeśli numer minuty nie mieści się w przedziale 0-59 nie jest to minuta - funkcja zwraca NULL
			 	//return 'Nieprawidłowy nr minuty';
			 	return NULL;
			 }
			
			//jeżeli kod dotarł tutaj to data jest poprawnie przekształcona na słowną i format godziny zgadza się z HH:MM
			//zwraca np. 27 listopada 2014 12:34
			return $data_zamieniona.$lacznik.$godzina;
		}
		else{
		//jeśli $godzina po rozdzieleniu względem : nie ma dwóch elementów to na pewno nie jest to HH:MM
		//funkcja zwraca NULL
			//return 'Godzina nie ma 2 elementów, tylko '.count($godzina_tab);
			return NULL;
		}
		
	}
}


function zamienDateNaTekst($data, $bez_roku=FALSE){
//zamienia datę w formacie pods (RRRR-MM-DD) na tekst np 2014-11-15 -> 15 listopada 2014
//jeśli z jakiegoś powodu konwersja się nie udała funkcja zwraca NULL
//$bez_roku == TRUE powoduje, że data zostanie zwrócona bez roku

	 if (strlen($data)==10){
	 //jeśli string ma długość 10 znaków, najprawdopodobniej jest to RRRR-MM-DD
		 //$tablica_data = explode('-',$data); //rozdzielenie tablicy na rok $tablica_data[0], miesiąc i dzień
		 if (count($tablica_data = explode('-',$data))==3)
		 //jeśli tablica_data ma trzy części po rozdzieleniu to jest szansa, że jest to data w formacie RRRR-MM-DD
		 {
			 $rok = $tablica_data[0];
			 $miesiac = $tablica_data[1];
			 $dzien = $tablica_data[2];
			 
			 if(!is_numeric($rok) || !is_numeric($miesiac) || !is_numeric($dzien)){
			 //jeśli $rok, $miesiac albo $dzien nie jest liczbą liczbą zwraca NULL
			 	//return '$rok, $miesiac albo $dzien nie jest liczbą liczbą';
			 	return NULL;
			 }
			 
			 settype($dzien, 'integer');
			 if(($dzien < 1) || ($dzien > 31)){
			 //jeśli numer dnia nie mieści się w przedziale 1-31 nie jest to dzień - funkcja zwraca NULL
			 	//return 'Nieprawidłowy nr dnia';
			 	return NULL;
			 }
			 
			 
			 settype($miesiac, 'integer');
			 if(($miesiac < 1) || ($miesiac > 12)){
			 //jeśli numer miesiąca nie mieści się w przedziale 1-12 nie jest to miesiąc - funkcja zwraca NULL
			 	//return 'Nieprawidłowy nr miesiąca';
			 	return NULL;
			 }
			 
			 settype($rok , 'integer');
			 
			 if(is_null($miesiac_slownie = ZamienMiesiacLiczbowyNaSlownyOdmieniony($miesiac))){
			 //jeśli funkcja ZamienMiesiacLiczbowyNaSlownyOdmieniony zwróciła NULL (nie udało się zamienić miesiąca liczbą na słowny
			 //cała funkcja zamienDateNaTekst też zwraca NULL
			 	 //return 'nie udało się zamienić miesiąca liczbą na słowny';
				 return NULL;
			 }
			 else{
			 //jeśli ZamienMiesiacLiczbowyNaSlownyOdmieniony nie zwróciła NULL - udało się zamienić miesiąc liczbą na słowny
			 //POŻĄDANE WYJŚCIE FUNKCJI zamienDateNaTekst
			 //np. 15 listopada 2014
			 	if(!$bez_roku){
					//jeśli nie wybrano opcji $bez_roku == TRUE
			 		return $dzien.' '.$miesiac_slownie.' '.$rok;
				}
				else
					return $dzien.' '.$miesiac_slownie;
			 }
		 }
		 else{
	     //jeśli tablica_data nie ma trzech części po rozdzieleniu (rok, miesiąc, dzień) zwracany jest NULL
		 	//return 'Nie ma trzech części. Są '.count($tablica_data);
			return NULL;
		 }
	 }
	 else{
     //jeśli $data nie ma długości 10 znaków to nie jest na pewno RRRR-MM-DD - zwracany jest NULL
	 	//return 'Nie ma długości 10 znaków. Ma długość '.strlen($data);
	 	return NULL;
	 }
 }//zamienDateNaTekst
 
 function pobieczCzescDaty($czescDaty,$dataGodz)
 //konwertuje string w formacie daty na datę i pobiera jej część
 //część do pobrania definiuje $czescDaty (zgodnie z formatem DateType funkcji date
 {
	 if (gettype($dataGodz)=='string')
	 //jeśli $dataGodz jest stringiem dokonuje konwersji i pobrania
	 {
		$dataGodz = strtotime($dataGodz);
	 	return date($czescDaty,$dataGodz);
	 }
	 else 
	 //jeśli nie jest stringiem zwraca tekst błędu
	 	return 'Błąd: functions - pobierzCzescDaty';
	 
 }//function pobieczCzescDaty($dataGodz)
 
//Funkcje używane przez funkcję zamienDateNaTekst -----------------------

function ZamienMiesiacLiczbowyNaSlownyOdmieniony($miesiac)
//zamienia miesiąc określony liczbą (gdzie 1 to styczeń!) na słowny - zwracany słowny w zależności jaka tablica została podstawiona
//jeśli konwersja nieudana zwraca NULL
{
	global $miesiaceOdmienione;
	
	if(($miesiac>0)&&($miesiac<13))
	{
		return $miesiaceOdmienione[$miesiac-1];
	}
	else{
		return NULL;
	}
}//eof ZamienMiesiacLiczbowyNaSlowny
 
function zamienDzienTygodniaLiczbowyNaSlowny($dzienTygodnia, $czyOdmienione = false)
//Funkcja zamienia numer dnia tygodnia (0 - nd, 6 - sb) na nazwę słowną
//Jeśli $czyOdmienione == true to na odmienioną, np. (od) piątku, niedzieli
{
	global $dniTygodnia, $dniTygodniaOdmienione;
	if(!$czyOdmienione)
	{
		return $dniTygodnia[$dzienTygodnia];
	}
	else
		return $dniTygodniaOdmienione[$dzienTygodnia];
	
}//function zamienDzienTygodniaLiczbowyNaSlowny($dzienTygodnia)

function printZawartoscTabeli($tabela, $separator=', '){
	$wynik = '';
	if (count($tabela)>0){
		//return 'k: '.count($tabela);
		for($i=0; $i<count($tabela)-1; $i++){
			$wynik .= $tabela[$i].$separator;
		}
		$wynik .= $tabela[count($tabela)-1];
	}
	return $wynik;
}

function printOdnosnikDoTaksonomii($tabelaNazw,$tabelaSlugow,$nazwaTaksomomii,$klasaCss=NULL, $separator=' '){
//funkcja używana przy pods, generuje kolejnych listę odnośników do taksonomii
//np. dla wydarzenia które jest powiązane z taksonomią 'kategorie'
//Dane wejściowe:
//$tabelaNazw - tabela nazw taksonomii - będą się wyświetlały jako tekst odnośnika 
//		(pozyskane np. przez $kategorie_name = $pods->field('kategorie.name');
//$tabelaSlugow - tabela slugów - jako część adresu
//		(pozyskane np. przez $kategorie_slug = $pods->field('kategorie.slug');
//$nazwaTaksomomii - np. kategorie - jeśli ustawione jest, że działa www.strona.pl/taksonomie/ to będzie działającym elementem adresu odnośnika
//$separator - znak lub znaki rozdzielające poszczególne wpisy (jeśli jest więcej niż 1), domyślnie spacja
//$klasaCss - klasa, którą będzie miał każdy odnośnik, domyślnie NULL (brak klasy)
//
//Jeśli się coś nie udało - zwraca NULL
	
	if(count($tabelaNazw)!=count($tabelaSlugow)){
	//sprawdzenie czy tabela nazw i slugów ma taką samą liczbę elementów - jeśli nie, jest to błąd, zwraca NULL
		return 'Tablice o różnych rozmiarach';
		//return NULL;
	}
	
	if(!is_null($klasaCss)){
	//jeśli został podany parametr $klasaCss to taka będzie klasa nadana wszystkim odnośnikom
		$klasa = ' class="'.$klasaCss.'"	';
	}
	
	$wynik = NULL;
	for($i=0; $i<count($tabelaNazw)-1; $i++){
		$wynik .= '<a href="'.home_url().'/'.$nazwaTaksomomii.'/'.$tabelaSlugow[$i].'"'.$klasa.'>'.$tabelaNazw[$i].'</a>'.$separator;
	}
	$wynik .= '<a href="'.home_url().'/'.$nazwaTaksomomii.'/'.$tabelaSlugow[count($tabelaNazw)-1].'"'.$klasa.'>'.$tabelaNazw[count($tabelaNazw)-1].'</a>';
	
	return $wynik;
}//printOdnosnikDoTaksonomii


//---------------funkcje używane przy wyświetlaniu kursów (używane w edukacja.php i edukacja_single.php)

function rysujWierszTabeliKursow($pods, $kolor_tabeli_kursu, $wyswNazweKursu = true){
	//Funkcja rysująca wiersz w tabeli kursów
	//Parametr $kolor_tabeli_kursu służy do naprzemiennego kolorowania (dwa kolory) wierszy - kursów parzystych i niemaprzystych
	//Parametr $wyswietlanaKategoria to slug kategorii kursu do której musi należeć kurs, żeby został wyświetlony
	//Jeśli jest NULL (standardowo) to kategoria nie ma znaczenia 
	//$wyswNazweKursu określa czy wyświetlany ma być także wiersz z nazwą kursu - odnośnikiem 
						
						if(empty($kolor_tabeli_kursu)){
							$kolor_tabeli_kursu = 0;
						}
						

							$title = $pods->display('name');					
							
							$permalink = $pods->field('permalink' );
							
							$prowadzacy = $pods->display('prowadzacy');
							$lokalizacje = $pods->field('lokalizacje.name');
							$lokalizacje = $lokalizacje[0];
						
							$lokalizacje_adres_slug = $pods->field('lokalizacje.adres.slug');
							$lokalizacje_adres_slug = $lokalizacje_adres_slug[0];
							
							$komentarz = $pods->display('komentarz');
							
							$kategorie_name = $pods->field('kategorie_kursu.name');
							$kategorie_slug = $pods->field('kategorie_kursu.slug');
	
							$grupa_kursowa = $pods->field('grupy_kursowe');
	
							?>
								<tr><td colspan="4" class="tabelaKursow_naglowek">
								
                                <?php if($wyswNazweKursu){ 
										//jeśli $wyswNazweKursu==true (domyślnie) wyświetlana jest nazwa kursu - odnośnik?>
                                	<a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"><h2><?php echo $title ?></h2></a>
                                <?php }//if($wyswNazweKursu) ?>
								
								
								<?php 
									//wyświetlanie etykiet kategorii
									for($i=0; $i < count($kategorie_name); $i++){
											if(!empty($kategorie_slug[$i]))
											{
												echo '<a href="'.home_url().'/kategorie-edukacji/'.$kategorie_slug[$i].'/" class="kategoria">'.$kategorie_name[$i].'</a> ';
											}//if(!empty($kategorie_slug[$i]))
										}//for($i=0; $i < count($kategorie_name); $i++)
								?>
								</td></tr>
								
								<tr class="kolor_tabeli_kursu<?php echo $kolor_tabeli_kursu ?>">
									<td colspan="2">Prowadzący: <?php echo $prowadzacy; ?></td>
									<td colspan="2"><a href="<?php echo home_url().'/adresy/'.$lokalizacje_adres_slug ?>"><?php echo $lokalizacje ?></a></td>
								</tr>
								
								<?php
								//Jeśli jest wpisany komentarz dla wydarzenia to wyświetlany nad grupami kursowymi
								if(!empty($komentarz)){
	
									?>
										<tr class="kolor_tabeli_kursu<?php echo $kolor_tabeli_kursu ?>" ><td colspan="4"><?php echo $komentarz ?></td></tr>
								   
									<?php
	
								}//if(!empty($komentarz))
								 ?>
								
								<tr class="kolor_tabeli_kursu<?php echo $kolor_tabeli_kursu ?> tabelaKursow_etykiety">
									<td>Grupa</td>
									<td>Dni zajęć</td>
									<td>Godziny&nbsp;zajęć</td>
									<td>Opłata</td>
								  </tr>
								
							<?php
							if ( !empty( $grupa_kursowa ) && is_array($grupa_kursowa) ) {
								
								foreach ( $grupa_kursowa as $rel ) {
									//get id for related post and put in id
									$id = $rel[ 'ID' ];
									get_the_title( $id );
	
									//pobranie wartości pól 
									$oplata = get_post_meta( $id, 'oplata', true );
									
									//pętla zliczająca ile dni dla poszczególnej grupy kursowej jest wypełnionych - potrzebne to później do rowspan
									$ile_dni = 0;
									for($nr_dnia = 1; $nr_dnia < 8; $nr_dnia++){
										$dzien_tygodnia= get_post_meta( $id, 'dzien_tygodnia_'.$nr_dnia, true );
										if(!empty($dzien_tygodnia)){
											$ile_dni++;
										}//if(!empty($dzien_tygodnia)){
									}//for($nr_dnia = 1; $nr_dnia < 8; $nr_dnia++){
									
									//pętla przebiegająca wszystkie dni tygodnia dla danej grupy kursowej
									//jeśli wypełniony jest dzień tygodnia (wybrano pon, wt itd) to wyświetla linię
									for($nr_dnia = 1; $nr_dnia < 8; $nr_dnia++){
										$dzien_tygodnia= get_post_meta( $id, 'dzien_tygodnia_'.$nr_dnia, true );
										if(!empty($dzien_tygodnia)){
											$godziny_dnia_tekstowo = get_post_meta( $id, 'godziny_'.$nr_dnia.'_tekstowo', true );		
											
											?>
											<tr class="kolor_tabeli_kursu<?php echo $kolor_tabeli_kursu ?>">
											<?php
											if($nr_dnia==1){
											//sprawdzanie czy jest to pierwszy dzień dla danej grupy - tylko wtedy dodawane jest to pole, bo ma rowspan dla wszystkich dni grupy
												echo '<td rowspan="'.$ile_dni.'">'.get_the_title( $id ).'</td>';
											}//if($nr_dnia==1
											
											echo '<td>'.$dzien_tygodnia.'</td>';
											if(!empty($godziny_dnia_tekstowo)){
												//sprawdzenie, czy podano opisowe godziny - jeśli tak to one są wyświetlane zamiad godziny od i do
												echo '<td>'.$godziny_dnia_tekstowo.'</td>';
											}//if(!empty($godziny_dnia_tekstowo))
											else{
												//jeśli nie podano opisowych godzin pobierane i wyświetlane są godziny od do
												$od_godziny = get_post_meta( $id, 'od_godziny_'.$nr_dnia, true );
												$do_godziny = get_post_meta( $id, 'do_godziny_'.$nr_dnia, true );
												echo '<td>'.$od_godziny;
												//jeśli podana jest godzina zakończenia to jest wyświetlana w komórce z myślnikiem
												if(!empty($do_godziny)){
													echo ' - '.$do_godziny;
												}//if(!empty($do_godziny))
												echo '</td>';
											}//else if(!empty($godziny_dnia_tekstowo))
											
											if($nr_dnia==1){
											//sprawdzanie czy jest to pierwszy dzień dla danej grupy - tylko wtedy dodawane jest to pole, bo ma rowspan dla wszystkich dni grupy
												echo '<td rowspan="'.$ile_dni.'">'.$oplata.'</td>';
											}//if($nr_dnia==1
											?>
											</tr>
											<?php
										}//if(!empty($dzien_tygodnia))
									}//for($nr_dnia = 1; $nr_dnia < 8; $nr_dnia++)
									
	
								} //end of foreach
									?><tr>
									<td colspan="4" class="tabelaKursow_przerwa"><br /></td>
									</tr><?php
							}//if ( !empty( $grupa_kursowa ) && is_array($grupa_kursowa) )
				
					return $kolor_tabeli_kursu = ++$kolor_tabeli_kursu % 2;
										
}//rysujWierszTabeliKursow($pods, $kolor_tabeli_kursu)

function rysujWierszNaglowkaKategoriiGlownej($wyswietlono_wiersz_kategorii_glownej, $kategoria_glowna, $zdefiniowane_kategorie_kursow,  $zdefiniowane_kategorie_kursow_nazwy){
//Funkcja rysująca wiersz-nagłówek kategorii głównych w tabeli kursów - używana jest jeśli następuje grupowanie kursów w tabeli czyli jeśli wybrano kategorię 'wszystkie' lub kategorię nie będącą kategorią główną

//Zwraca true żeby ustawić flagę, że już wyświetlono ten wiersz
	if(!$wyswietlono_wiersz_kategorii_glownej){
	//jeśli nie wyświetlono wiersza (nagłówka) kategorii głównej to jest on wyświetlany a zmienna $wyswietlono_wiersz_kategorii_glownej = true
	//zrobiono tak, bo ten wiersz może być wyświetlany tylko, gdy znaleziono jakiś kurs w danej kategorii (wynika to z problemu AND w klauzuli WHERE
	//parametr $kategoria_glowna to slug kategorii
	//parametry $zdefiniowane_kategorie_kursow i $zdefiniowane_kategorie_kursow_nazwy służą do zamiany slug'u kategorii na jej nazwę
		$nazwa_kategorii_glownej = $zdefiniowane_kategorie_kursow_nazwy[array_search($kategoria_glowna, $zdefiniowane_kategorie_kursow)];
		echo '<tr><td colspan="4" class="tabelaKursow_naglowek"><h1>Zajęcia '.$nazwa_kategorii_glownej.'</h1></td></tr>';
		return true;
	}//if(!$wyswietlono_wiersz_kategorii_glownej)
	else{
	//jeśli $wyswietlono_wiersz_kategorii_glownej jest już true to funkcja nic nie robi ale musi nadal zwracać true
		return true;
	}//else od if(!$wyswietlono_wiersz_kategorii_glownej)
}//rysujWierszNaglowkaKategoriiGlownej($wyswietlono_wiersz_kategorii_glownej, $kategoria_glowna)

//-----------------------

?>