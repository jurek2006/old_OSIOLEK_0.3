var $ = jQuery.noConflict();

	function obslugaSlidera(slider,input, min = 0, max = 100, step = 10){
	// funkcja dodająca działanie slidera do pola tekstowego formularza
	// Aby użyć mysimy mieć input np. <input type="text" id="inputX" value="100">'
	// i element na slider np. <div id="sliderX"></div>
	// Dodatkowo sprawia, że pole tekstowe jest nieaktywne bezpośrenio dla użytkownika
	// 
	// Wtedy możemy wywołać funkcję 
	// np. obslugaSlidera("#inputX", "#sliderX"); 
	// lub obslugaSlidera("#inputX", "#sliderX", 100, 1000, 10);
	// gdzie trzy ostatnie parametry to min wartość, maksymalna i step(krok)
	// domyślnie 0, 100, 1

		// inicjalizacja i ustawienie slidera
		$(slider).slider({
			min: min, 	//wartośc minimalna
			max: max,	//wartość maksymalna
			step: step,	//krok (step)
		  	value: $(input).val(), //wartośc początkowa, pobierana z pola input
		  	// obsługa zdarzenia przesunięcia suwaka, wówczas wartość suwaka przekazywana jest do pola input
		  	slide: function( event, ui ) {
		  	  	$(input).val($(this).slider( "value" ));
		  	}
		});

		// Zdezaktywowanie bezpośreniej obsługi pola input
		$(input).prop('readonly', true).focus(function(){
			$(this).blur();
		});
	}//obslugaSlidera()
