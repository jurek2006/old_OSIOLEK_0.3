<?php 
// =======================================================================================================
// --------------------- STRONY EXPORT PODS i IMPORT PODS (wyświetlana w dashboard) - w jednym Menu 'Export i import Pods'
// włączana za pomocą require do functions.php
// =======================================================================================================

// Dodanie menu w dashboardzie
function exp_imp_pods_add_admin_page(){
// funkcja tworząca menu

	// utworzenie menu
	add_menu_page('Export i import Pods', 'Exp - Imp Pods', UPR_DSH_MENU_EXPORT_PODS, 'exp_imp_pods', 'export_pods_create_page', '
dashicons-external', 6);

	// utworzenie elementów podmenu
	// - pierwszy element - Export - wyświetlany automatycznie po kliknięciu na menu
	add_submenu_page('exp_imp_pods', 'Export Pods', 'Export Pods', UPR_DSH_MENU_EXPORT_PODS, 'exp_imp_pods', 'export_pods_create_page');
	// Podmenu - drugi element
	add_submenu_page('exp_imp_pods', 'Import Pods', 'Import Pods', UPR_DSH_MENU_IMPORT_PODS, 'import_pods_import', 'import_pods_create_page');

}
// dodanie haka do funkcji tworzącej menu (do admin_menu - wuruchamiane, kiedy jest tworzone menu dashboard)
add_action('admin_menu', 'exp_imp_pods_add_admin_page');

function export_pods_create_page(){
// funkcja tworząca stronę Export Pods w dashboardzie

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-export-pods.php');
}

function import_pods_create_page(){
// funkcja tworząca stronę Import Pods w dashboardzie

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-import-pods.php');
}

?>