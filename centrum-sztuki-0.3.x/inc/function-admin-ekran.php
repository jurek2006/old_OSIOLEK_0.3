<?php 
// =======================================================================================================
// --------------------- STRONA ZARZĄDZANIA USTAWIENIAMI EKRAN KASA (wyświetlana w dashboard)
// włączana za pomocą require do functions.php
// =======================================================================================================

// Dodanie menu w dashboardzie
function ekran_kasa_add_admin_page(){
// funkcja tworząca menu

	// utworzenie menu
	add_menu_page('Ustawienia ekranu repertuaru w kasie', 'Ekran repertuaru', UPR_DSH_MENU_KASA_EKRAN, 'ekran_kasa', 'ekran_kasa_create_page', 'dashicons-desktop', 6);

	// dodanie strony w admin, która nie wyświetla się w menu dashboard (dlatego pierwszy parametr - parent-slug jest null)
	add_submenu_page(null, 'Zmiana tytułów', 'Zmiana tytułów', UPR_DSH_MENU_KASA_EKRAN, 'admin_ekran_change_title', 'admin_ekran_change_title_create_page');

}
// dodanie haka do funkcji tworzącej menu (do admin_menu - wuruchamiane, kiedy jest tworzone menu dashboard)
add_action('admin_menu', 'ekran_kasa_add_admin_page');

function ekran_kasa_create_page(){
// funkcja tworząca stronę w dashboardzie

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-ekran-page.php');
}

function admin_ekran_change_title_create_page(){
// funkcja tworząca stronę admin-ekran-change-title w admin

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-ekran-change-title.php');
}

?>