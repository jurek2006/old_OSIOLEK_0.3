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

}
// dodanie haka do funkcji tworzącej menu (do admin_menu - wuruchamiane, kiedy jest tworzone menu dashboard)
add_action('admin_menu', 'ekran_kasa_add_admin_page');

function ekran_kasa_create_page(){
// funkcja tworząca stronę w dashboardzie

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-ekran-page.php');
}

?>