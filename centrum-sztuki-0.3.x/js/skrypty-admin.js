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

	function obslugaButtonToggle(button,objectToToggle,valueWhenShown = null, valueWhenHidden = null){
	// funkcja dodająca działanie toggle (pokazywanie/ukrywanie) do button
	// Przykładowe wywołanie:
	// obslugaButtonToggle("#szczegolyButton",'.tabela'); gdzie:
	// #szczegolyButton to id buttona, a .tabela to klasa elementu do pokazywania/ukrywania
	// jeśli zdefiniowane są etykiety przycisku valueWhenShown i valueWhenHidden to podmienia je przy pokazywaniu/ukrywaniu
	// w tym przypadku standardowe użycie to np.:
	// obslugaButtonToggle("#szczegolyButton",'.tabela',"Ukryj szczegóły", "Pokaż szczegóły");

		// ustawienie val początkowych buttona, jeśli zdefiniowano w wywołaniu klasy valueWhenShown i valueWhenHidden
		// na podstawie tego czy objectToToggle jest ukryty czy wyświetlony
		if(valueWhenShown != null && valueWhenShown != null){
			if($(objectToToggle).is(':hidden')){
				$(button).val(valueWhenHidden);
			}
			else{
				$(button).val(valueWhenShown);
			}
		}

		$(button).click(function(){
			
			if(valueWhenShown != null && valueWhenShown != null){
				if($(objectToToggle).is(':hidden')){
					$(this).val(valueWhenShown);
				}
				else{
					$(this).val(valueWhenHidden);
				}
			}
			$(objectToToggle).slideToggle();
		});
	}//obslugaButtonToggle(button,objectToToggle)
