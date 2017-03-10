// skrypt dołączany (w functions.php) do stron w "kokpicie"
//dołączany do stron (formularzy) dodających postu (post-new.php) i edytujących posty (post.php)
var $ = jQuery.noConflict();

jQuery(document).ready(function(){

	//ukrycie bocznych box'ów 'adresy' i 'kategorie' przy dodwaniu i edycji postów
	//zablokowanie możliwości ich włączenia w "opcje ekranu"
	$('#tagsdiv-adresy').hide();
	$('#tagsdiv-adresy-hide').prop("disabled", true);

	$('#tagsdiv-kategorie').hide();
	$('#tagsdiv-kategorie-hide').prop("disabled", true);

	
	if($('.post-type-wydarzenia').length > 0){
	//jeżeli formularz dodawania/edycji postu dotyczy wydarzeń
	//do etykiety pola "Data i godzina wydarzenia" dopisywana jest informacja, że pole wymagane
	//jeśli przy kliknięciu Opublikuj/Zaktualizuj nie jest wypełnione "Data i godzina wydarzenia"
	//następuje zablokowanie "wyzwolenia" przycisku i komunikat o niewypełnionym polu
	// właściwie jest to już niepotrzebne bo wordpress też już potrafi sprawdzać to "po ludzku" na żywo
		$('#publish').click(function(evt){
			if($('#pods-form-ui-pods-meta-data-i-godzina-wydarzenia').val() == ''){
				alert('Aby zapisać musi być wypełnione pole "Data i godzina wydarzenia"');
				return false;
			}
		});

		var labelHtml = $('.pods-form-ui-label-pods-meta-data-i-godzina-wydarzenia').html();
		labelHtml += '* (pole wymagane)';
		$('.pods-form-ui-label-pods-meta-data-i-godzina-wydarzenia').html(labelHtml);
	}
	else if($('.post-type-cennik_dni_kalendarz').length > 0){
	// jeżeli jesteśmy na stronie dodawania/edycji pods cennik_dni_kalendarz
	// ukrywamy tytuł (pole input na niego) i podczas zapisywania wpisu jako tytuł podstawiamy wybraną datę i etykietę ceny
	// np.: 2017-02-02 - dkf
		$('#title').hide();

		$('#publish').click(function(evt){

			$('#title').val($('#pods-form-ui-pods-meta-data').val() + ' - ' +  $('#pods-form-ui-pods-meta-cennik option:selected').text());

		});
	}

	// post-new-php - dodawanie nowego postu
	// post-php - edycja postu
	// post-type-wydarzenia - dodawanie lub edycja postu o typie wydarze
	// post-type-filmy
/*	if($('.post-php').length > 0){
		console.log("Edycja");
	}

	if($('.post-new-php').length > 0){
		console.log("Dodawanie nowego");
	}

	if($('.post-type-wydarzenia').length > 0){
		console.log("Kategoria: wydarzenia");
		//$('#publish').prop('disabled',true);
	}

	if($('.post-type-filmy').length > 0){
		console.log("Kategoria: filmy");
	}*/

});

