<?php 
// =======================================================================================================
// --------------------- STRONA EXPORT - IMPORT PODS (wyświetlana w dashboard)
// włączana za pomocą require do functions.php
// =======================================================================================================

// Dodanie menu w dashboardzie
function exp_imp_pods_add_admin_page(){
// funkcja tworząca menu

	// utworzenie menu
	add_menu_page('Export - import Pods', 'Exp-Imp Pods', UPR_DSH_MENU_EXP_IMP_PODS, 'exp_imp_pods', 'exp_imp_pods_create_page', '
dashicons-external', 6);

}
// dodanie haka do funkcji tworzącej menu (do admin_menu - wuruchamiane, kiedy jest tworzone menu dashboard)
add_action('admin_menu', 'exp_imp_pods_add_admin_page');

function exp_imp_pods_create_page(){
// funkcja tworząca stronę w dashboardzie

	// wczytanie szablonu strony
	require_once(get_template_directory(). '/inc/admin-exp-imp-pods.php');
}

?>