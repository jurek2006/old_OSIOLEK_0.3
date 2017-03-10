<?php 
	// =======================================================================================================
	// --------------------- STRONA USTAWIEŃ DLA FUNCTIONS
	// włączana za pomocą require do functions.php
	// =======================================================================================================

	// TESTOWE
	// -------
	// tryb testowy - jeśli ustawiony na true wyświetlane są dodatkowe komunikaty (używane za pomocą funkcji testoweConsoleLog)
	define("TRYB_TESTOWY", false);
	// ------------------------------------------------------------------------------------------------------------------------
	
	// SETTINGS
	// ---------
	//PRZESUNIECIE_CZASU określa różnicę pomiędzy czasem serwera a czasem rzeczywistym
	//np. serwer ma czas 8:30 a jest rzeczywiście 10:30 więc stała ma wartość "+2 hour"
	//(stała używana jest w funkcji pobierzDateTeraz() wywoływanej przy każdym pobieraniu aktualnego czasu
	define("PRZESUNIECIE_CZASU", "+1 hour");

	// ADRES SYSTEMU BILETOWEGO VISUALTICKET DLA CSO
	define("ADRES_VISUALTICKET", "https://s7.systembiletowy.pl/cso/");

	// ------------------------------------------------------------------------------------------------------------------------

	// UPRAWNIENIA
	// -----------

	//stała określa, jakie uprawnienia musi mieć użytkownik do strony importu projekcji
	//(używane przy wyświetlaniu treści strony w szablonie ale także do dołączania skryptu .js)
	define("UPR_IMPORT_PROJEKCJI", "publish_posts");
	
	//stała określająca uprawnienia potrzebne do dostępu do strony kino-zarządzanie (kino-zarzadzanie.php)
	define("UPR_KINO_ZARZADZANIE", "publish_posts"); 
	
	//uprawnienia potrzebne do wyświetlania menu top-user-navigation zamiast top-navigation
	define("UPR_MENU_USER", "publish_posts"); 

	// uprawnienia potrzebne do wyświetlania w dashboard menu zarządzania ekranem repertuaru w kasie
	define("UPR_DSH_MENU_KASA_EKRAN", 'edit_ekran_kasa_biletowa');

	// ------------------------------------------------------------------------------------------------------------------------

	// NAZWY PODS
	// -----------

	define("POD_EKRAN_KASA_USTAWIENIA", 'ekran_kasa_ustawienia');

	define("POD_EKRAN_KASA", 'ekran_kasa');
?>