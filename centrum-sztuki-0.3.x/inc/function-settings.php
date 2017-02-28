<?php 
	// =======================================================================================================
	// --------------------- STRONA USTAWIEŃ DLA FUNCTIONS
	// włączana za pomocą require do functions.php
	// =======================================================================================================

	// TESTOWE
	// -------
	// tryb testowy - jeśli ustawiony na true wyświetlane są dodatkowe komunikaty (używane za pomocą funkcji testoweConsoleLog)
	define("TRYB_TESTOWY", true);
	// ------------------------------------------------------------------------------------------------------------------------
	
	// SETTINGS
	// ---------
	//PRZESUNIECIE_CZASU określa różnicę pomiędzy czasem serwera a czasem rzeczywistym
	//np. serwer ma czas 8:30 a jest rzeczywiście 10:30 więc stała ma wartość "+2 hour"
	//(stała używana jest w funkcji pobierzDateTeraz() wywoływanej przy każdym pobieraniu aktualnego czasu
	define("PRZESUNIECIE_CZASU", "+1 hour");

	// ADRES SYSTEMU BILETOWEGO VISUALTICKET DLA CSO
	define("ADRES_VISUALTICKET", "https://s7.systembiletowy.pl/cso/");

	// USTAWIENIA EKSPORTU i IMPORTU danych PODS (używane w inc/admin-import-pods.php i inc/admin-export-pods.php)
	// ------

	// BLOKOWANIE OPCJI IMPORTU PODS (w inc/admin-import-pods.php) na wersji produkcyjnej (czyli na www.kultura.olawa.pl)
	define("BLOKUJ_IMPORT_PODS", true);

	// alias home_url przy eksportowaniu danych do tekstu (json) i później importowaniu
	define("HOME_URL_ALIAS", "{home_url}");

	define("PODS_FILMY","filmy"); //nazwa pods służącego do zapisywania filmów (używana na razie tylko przy import/export)
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

	// uprawnienia potrzebne do wyświetlania w dashboard strony export pods (i menu export - import pods)
	define("UPR_DSH_MENU_EXPORT_PODS", 'export');

	// uprawnienia potrzebne do wyświetlania w dashboard strony import pods 
	define("UPR_DSH_MENU_IMPORT_PODS", 'import');
?>