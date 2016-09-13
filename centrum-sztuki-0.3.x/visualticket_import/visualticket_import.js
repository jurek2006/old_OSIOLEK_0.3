var $ = jQuery.noConflict();

// nazwa kategorii wydarzeń (w systemie VisualTicket)
const kategoriaRepertuaru = 'repertuar_kina';

//ścieżka do pliku php pobierającego dane projekcji z VisualTicket i zwracającego je jako XML
//ścieżka jest wczytywana w funkcji ready (pobierana ścieżka home_url czyli home strony z import-projekcji.php)
//Dla strony jest to "http://www.kultura.olawa.pl/XMLGetter.php"

// const path_XMLProjekcjeGetter = 'http://www.kultura.olawa.pl/wp-content/jurekRozszerzenia/XMLGetter.php';
file_XMLProjekcjeGetter = '/wp-content/jurekRozszerzenia/XMLGetter.php';




//funkcja ready

jQuery(document).ready(function(){
	
	//ścieżka do pliku pobierającego dane o projekcjacj z VT (home_url przekazywany z import-projekcji.php)
	//zapewnia bezproblemowe działanie zarówno na kultura.olawa.pl jak i na testy_laboratorium
	path_XMLProjekcjeGetter = home_url + file_XMLProjekcjeGetter;


	//okno dialogowe wczytywane na początku, a ukrywane, gdy zostaną pobrane wszystkie dane
	//okno jest modal, dlatego dopóki nie zniknie, nie można nic zrobić
	$('#wait').dialog({
		modal: 			true,
		hide: 			{effect: 'fadeOut', duration: 2000},
		dialogClass: 	"no-close"
	});

	$('#error').dialog({
		hide: 		{effect: 'fadeOut', duration: 2000},
		autoOpen: 	false
	});
	
	

	wczytajProjekcjeVT();

	$('input[name="zmienStatusPublikacji"]').click(function(){
	//obsługa zdarzenia - kliknięcia przycisku "Zmień status zaznaczonych"

		//dla każdego zaznaczonego za pomocą checkbox'a wiersza - nadaje nowy status (publikacji projekcji) oraz nadaje klasę css 'zmienionaProjekcja'
		$( "input:checked" ).each(function (){

			var id_online = $(this).parents('tr').find('input[name="id_online[]"]').val(); 
			var nadawanyStatus = $('select[name="statusyPublikacji"]').val();

			var inputZmienionyStatus = $(this).parents('tr').find('input[name="zmienionyStatus[]"]'); //pole hidden o nazwie zmienionyStatus
			var id_pods_wordpress = inputZmienionyStatus.val(); //pobranie identyfikatora wordpressowego z pola hidden
			//jeśli nie zmieniano jeszcze statusu publikacji dla danej projekcji, wtedy zawartość pola (id_pods_wordpress) hidden zawiera tylko id wordpressowe

			if(inputZmienionyStatus.prop( "disabled") == false){
			//kiedy pole jest odblokowane, oznacza to, że zmieniano już status (i zawartośc pola to id_wordpressowe|status)
			//chcemy wyciągnąć z tego id_wordpressowe, bo status i tak się zmienia
				id_pods_wordpress = id_pods_wordpress.split("|"); 
				id_pods_wordpress = id_pods_wordpress[0]; //po rozdzieleniu względem znaku | będzie to pierwszy element
			}
			inputZmienionyStatus.val(id_pods_wordpress +"|"+ nadawanyStatus); //przekazaniue w wartości pola hidden w stylu 1000|draft gdzie draft to nadawany status publikacji a 1000 to id w wordpress (nie mylić z id_online projekcji)
			inputZmienionyStatus.prop( "disabled", false ); //wyłączenie disabled dla danego pola hidden
			$(this).parents('tr').find('.status_www').append().text(tlumaczenieStatusuPostow[nadawanyStatus]); //tablica tlumaczenieStatusuPostow przekazana z import-projekcji.php zawiera tłumaczenia statusów 
			$(this).parents('tr').addClass('zmienionaProjekcja');

			//odznaczenie przetworzonej już projekcji
			$(this).prop("checked", false); //odznaczenie checkbox'a
			$(this).parents('tr').removeClass('zaznaczone'); //odebranie klasy 'zaznaczone' (styl wyróżnienia zaznaczonych projekcji - wierszy)
			
		})
	})

	// dodanie datepicker z jQuery UI dla pola tekstowego #dataPoczatekSprzedazy umożliwiającego zmianę daty początku sprzedaży zaznaczonych (generowane w import-projekcji.php)
	$('#dataPoczatekSprzedazy').datepicker({
		numberOfMonths: 3,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});

	$('#zmienPoczatekSprzedazy').click(function(){
	//obsługa zdarzenia - kliknięcia przycisku "Zmień datę publikazji sprzedaży zaznaczonych" (generowane w import-projekcji.php)

		$( "input:checked" ).each(function (){
		//dla każdego zaznaczonego za pomocą checkbox'a wiersza - nadaje nową datę początku sprzedaży oraz nadaje klasę css 'zmienionaProjekcja'

			// publikacja_biletow_dzien
			// id_wordpress
			var input_publikacja_biletow_dzien = $(this).parents('tr').find('.publikacja_biletow_dzien');
			input_publikacja_biletow_dzien.val($('#dataPoczatekSprzedazy').val()); //przypisanie polu początku sprzedaży dla danej publikacji daty (lub pustej wartości) wybranej w polu wybierania daty
			//wyłączenie disabled dla pól publikacja_biletow_dzien i id_wordpress, żeby można było przekazać dane do import-projekcji.php o zmianie daty początku sprzedaży
			input_publikacja_biletow_dzien.prop("disabled", false);
			$(this).parents('tr').find('.id_wordpress').prop("disabled", false);

			$(this).parents('tr').addClass('zmienionaProjekcja');

			//odznaczenie przetworzonej już projekcji
			$(this).prop("checked", false); //odznaczenie checkbox'a
			$(this).parents('tr').removeClass('zaznaczone'); //odebranie klasy 'zaznaczone' (styl wyróżnienia zaznaczonych projekcji - wierszy)
		});
	});

	$('#zapiszButton').tooltip({
		content: $('#zapiszButtonTooltip').html()
	});

	$('#zapiszButton').click(function(evt){
	//obsługa zdarzenia kliknięcia przycisku zapisz - zapobiega jego działaniu, jeśli nie dokonano żadnych zmian w projekcjach
	//czyli nie przypisano żadnego filmu ani nie zmieniono dla żadnej projekcji statusu publikacji czy daty początku sprzedaży
		
		if(($('tr.zmienionaProjekcja').length == 0) && ($('tr.wypelnione').length == 0)){
		//nie dokonano zmian, jeżeli w tabli nie ma żadnego wiersza o klasie .wypelnione ani .zmienionaProjekcja
			return false; //zatrzymanie działania przycisku
		}
	})


});


function wczytajProjekcjeVT(){
	
	
	$.ajax({
        type: "GET",
		url: path_XMLProjekcjeGetter,
		dataType: "xml",
		success: function(xml) {

				/*=================================================== WYPEŁNIANIE TABELI #dodajProjekcje =============================================================*/
				
				$dodajProjekcje = $('#dodajProjekcje');
				var filmyVT = [];

				var outDodajProjekcjeTable = ''; //Zmienna do której zapisywana jest tabela, a następnie "na raz" jej zawartośc jest dodawana do strony

				//stworzenie nagłówka tabeli
				outDodajProjekcjeTable += (	'<tr>'
					                        +'<th>ID online</th>'
					                        +'<th>Film</th>'
					                        +'<th>3D</th>'
					                        +'<th>Wer. językowa</th>'
					                        +'<th>Data projekcji</th>'
					                        +'<th>Godz</th>'
					                        +'<th>Data publikacji sprzedaży</th>'
					                        +'<th colspan="2">W systemie biletowym</th>'
					                        +'<th>Na www</th>'
					                        +'<th><input type="checkbox" name="zaznacz_wszystko"/></th>' //checkbox umożliwiający zaznaczanie wszystkich (zapisanych) projekcji 
					                        //czyli tych, które w ogóle mają checkbox
					                    +'</tr>');


				var naglowekDnia = false;

				$(xml).find('repertoire').each(function(){
				//dla każdego (przyszłego i opublikowanego) terminu z VT 

					var id_online_value = $(this).attr('id'); //identyfikator projekcji zarówno w VT jak i na WWW(od online) [WARTOŚĆ]

					//sprawdzenie czy termin(projekcja) jest już wpisany na WWW - czy w tablicy projekcjeWWW znajduje się element projekcjeWWW[id_online_value]
					//jeśli jest on undefined, to projekcja nie została wpisana

					//pobranie tytułu projekcji
					var tytul = $(this).find('title').text();
					var idWydarzenia = $(this).find('event').attr('id'); //pobranie id wydarzenia (czyli wszystkich projekcji danego filmu)

					//pobranie daty i czasu terminu
					var dataCzasTerminu = new Date($(this).attr('timestamp')*1000);

					//pobranie daty i czasu rozpoczęcia sprzedaży
					var dataCzasRozpSprzed = new Date($(this).attr('date_of_distribution_timestamp')*1000);

					//pobranie kategorii wydarzeń
					var kategoria = $(this).find('category').text();

					//dodanie nagłówka "dniowego" do tabeli projekcji - sprawdza, czy pomiędzy projekcjami (w wierszach) jest różna data - jeśli tak, to wstawia nagłówek dni
					//warunek sprawdzania kategorii jest niezbędny do uwzględniania tylko wyświetlanych projekcji (terminów) - funkcja iteruje także po tych nie dależących do kategorii zdefiniowanej w kategoriaRepertuaru  
					if(kategoria == kategoriaRepertuaru){
						if(naglowekDnia != pobierzDate(dataCzasTerminu, 'yyyy-mm-dd')){
							outDodajProjekcjeTable +='<tr class="naglowekDnia"><td colspan="11">'+ pobierzDate(dataCzasTerminu, 'w,dd mt yyyy') +'</td></tr>';
						}
					
						naglowekDnia = pobierzDate(dataCzasTerminu, 'yyyy-mm-dd');
					}


					if(projekcjeWWW == null || projekcjeWWW[id_online_value] == undefined){
					//dla każdej projekcji z VT, która nie jest wpisana na WWW
					//wygenerowanie wiersza danych projekcji w tabeli #dodajProjekcje oraz dodanie odpowiadającego projekcji filmu do tabeli filmy VT
					//pierwsza część warunku sprawdza, czy jest wpisana w ogóle jakaś nadchodząca projekcja
					
						
						
						
						
						var id_online = '<input type="text" name="id_online[]" size="4" maxlength="4" readonly value="'+ id_online_value +'">'; //identyfikator projekcji zarówno w VT jak i na WWW(od online) [INPUT]
						var id_filmuWWW = '<input type="hidden" name="film_projekcji[]" class="film_projekcji">'; //pole hidden na identyfikator fimu na WWW
						var format2d3d = '<input type="text" name="format[]" class="format" size="2" maxlength="2" readonly>';
						var wersja_jezykowa = '<input type="text" name="wersja_jezykowa[]" class="wersja_jezykowa" size="5" readonly>';
						var dataTerminu = '<input type="text" name="data_projekcji[]" class="data_projekcji" size="8" readonly value="'+ pobierzDate(dataCzasTerminu, 'yyyy-mm-dd') +'">'; 
						var czasTerminu = '<input type="text" name="czas_projekcji[]" class="czas_projekcji" size="5" readonly value="'+ pobierzCzas(dataCzasTerminu, 'yyyy-mm-dd') +'">'; 
						
						//informacja o dacie początku sprzedaży w VT jest to pole formularza o innej nazwie niż w przypadku projekcji już zapisanych
						var publikacja_biletow_dzien = '<input type="text" name="data_publikacji[]" class="data_publikacji" readonly value="'+ pobierzDate(dataCzasRozpSprzed, 'yyyy-mm-dd') +'" >';

						//wiersz tabeli otrzymuje klasę z numerem id wydarzenia w VT - np. dla filmu o id wydarzenia 100 jest to film_100 (identycznie jak w tabeli #filmy)
						if(kategoria == kategoriaRepertuaru){
							
							outDodajProjekcjeTable +=('<tr class="film_'+ idWydarzenia +'">'
														+'<td>'+id_online+ '</td>'
														+'<td><span class="film">[nie wybrano]</span>'+' '+ id_filmuWWW +'</td>'
														+'<td>'+ format2d3d +'</td>'
														+'<td>'+ wersja_jezykowa +'</td>'
														+'<td>'+dataTerminu+'</td>'
														+'<td>'+czasTerminu+'</td>'
														+'<td>'+publikacja_biletow_dzien+'</td>'
														+'<td>'+ tytul +'</td>'
														+'<td>'+idWydarzenia+'</td>'
														+'<td class="status_www">Niezapisane</td>'
														+'<td></td>' //pole na checkbox dla projekcji już zapisanych na www (tutaj zawsze puste)
													+'</tr>');
							
							//Utworzenie obiektu film i sprawdzenie, czy jest on już w tablicy obiektów filmy (są to filmy z VT) 
							//jeśli go nie ma, jest dodawany do filmy
							//pozwala stworzyć tablicę filmów (wydarzeń) pobranych z VT bez powtórzeń
							var film = new Object;
								film.id = idWydarzenia;
								film.tytul = tytul;
							
							if(!idObjInArray(film, filmyVT,'id')){
								
								filmyVT.push(film);
							}
						}
					}
					else{
					//dla każdej projekcji, która jest już wpisana na WWW
					//wyświetlenie danych pobranych z WWW w wierszu projekcji	

						var id_online = '<input type="text" name="id_online[]" size="4" maxlength="4" readonly value="'+ id_online_value +'" disabled>'; //identyfikator projekcji zarówno w VT jak i na WWW(od online) [INPUT]
						var format2d3d = '<input type="text" name="format[]" class="format"  size="2" maxlength="2" disabled  value="'+ projekcjeWWW[id_online_value].format2d3d +'">';
						var wersja_jezykowa = '<input type="text" name="wersja_jezykowa[]" class="wersja_jezykowa" size="5" disabled value="'+ projekcjeWWW[id_online_value].wersja_jezykowa +'">';
						var dataCzasTerminu = new Date($(this).attr('timestamp')*1000);
						var dataTerminu = '<input type="text" name="data_projekcji[]" class="data_projekcji" size="8" value="'+ pobierzDate(dataCzasTerminu, 'yyyy-mm-dd') +'" disabled>'; 
						var czasTerminu = '<input type="text" name="czas_projekcji[]" class="czas_projekcji" size="5" readonly value="'+ pobierzCzas(dataCzasTerminu) +'" disabled>'; 
						var idWydarzenia = $(this).find('event').attr('id'); //pobranie id wydarzenia (czyli wszystkich projekcji danego filmu)
						var status_publikacji = projekcjeWWW[id_online_value].status_publikacji; //status publikacji danego terminu (draft, publish, pending, future)
						var id_wordpress = projekcjeWWW[id_online_value].id_wordpress; //id projekcji w wordpress

						//informacja o dacie publikacji biletów składa się z dwóch pól "publikacja_biletow_dzien" to data
						var publikacja_biletow_dzien = '<input type="text" name="publikacja_biletow_dzien[]" class="publikacja_biletow_dzien" readonly value="'+ projekcjeWWW[id_online_value].publikacja_biletow_dzien +'" disabled>'
														+'<input type="hidden" name="id_wordpress[]" class="id_wordpress" value="'+ id_wordpress +'" disabled >';

						//wiersz tabeli otrzymuje klasę z numerem id wydarzenia w VT (id_online) - np. dla filmu o id wydarzenia 100 jest to film_zapisany_100
						//nadanie wierszowi także klasy .zapisane żeby wyróżnić go wizualnie za pomocą css
							
						outDodajProjekcjeTable +=('<tr class="film_zapisany_'+ idWydarzenia +' zapisane">'
													+'<td>'+id_online+ '</td>'
													+'<td>'+ projekcjeWWW[id_online_value].tytul_filmu +'</td>'
													+'<td>'+ format2d3d +'</td>'
													+'<td>'+ wersja_jezykowa +'</td>'
													+'<td>'+dataTerminu+'</td>'
													+'<td>'+czasTerminu+'</td>'
													+'<td>'+publikacja_biletow_dzien+'</td>'
													+'<td>'+ tytul +'</td>'
													+'<td>'+idWydarzenia+'</td>'
													+'<td class="status_www">'+tlumaczenieStatusuPostow[status_publikacji]+'</td>' //Przetłumaczony status za pomocą tablicy tlumaczenieStatusuPostow
													+'<td><input type="checkbox" name="zaznacz"/><input type="hidden" name="zmienionyStatus[]" disabled value="'+id_wordpress+'"></td>'//pole na checkbox dla projekcji już zapisanych na www
												+'</tr>');
					}
				});//$(xml).find('repertoire').each(function()

				//dodanie tablicy z zawartości zmiennej outDodajProjekcjeTable do DOM strony
				$dodajProjekcje.append(outDodajProjekcjeTable);

				//obsługa zdarzenia zaznaczenia/odznaczenia checkbox'a (a w ten sposób zaznaczenia-odznaczenia projekcji - wiersza)
				$('input[name="zaznacz"]').change(function(){
				//obsługa zdarzenia zaznaczenia checkbox'a

				        if($(this).is(":checked")){
							$(this).parents('tr').addClass('zaznaczone');
						}
						else{
							$(this).parents('tr').removeClass('zaznaczone');
						}
				});

				//obsługa zdarzenia zaznaczenia checkbox'a w nagłówku
				$('input[name="zaznacz_wszystko"]').change(function(){
					if($(this).is(":checked")){

							$('input[name="zaznacz"]').each(function(){
								$(this).prop('checked', true);
								$(this).parents('tr').addClass('zaznaczone');
							})
						}
						else{
							$('input[name="zaznacz"]').each(function(){
								$(this).prop('checked', false);
								$(this).parents('tr').removeClass('zaznaczone');
							})
						}
				})

				/*=================================================== WYPEŁNIANIE TABELI #filmy =============================================================*/
				//Tabela ta zostaje wypełniona wszystkimi filmami (wydarzeniami) pobranymi z VT (są to tytuły filmów bez powtórzeń z systemu VT dla terminów, które zostały pobrane wyżej)
				//Umożliwia przypisanie każdemu filmowi VT filmu z WWW wraz z danymi projekcji tego filmu
				
				
				var outTable='';
				$.each(filmyVT,function(index,value){
					
					var wybierzFilmWWW = '<input type="text" class="wybierzFilmWWW" name="wybierzFilmWWW">';

					var idFilmu = '<input type="text" class="id_filmu" name="id_filmu">'; //Pole przekazujące id wybranego filmu (na www)

					var format2d3d = 	'<select name="format2d3d">'
											+'<option value="?">?</option>'
											+'<option value="">2D</option>' //jeśli wybrano 2D to przekazuje do tabeli projekcji pustą wartość
											+'<option value="3D">3D</option>'
										+ '</select>'

					var werJezykowa = 	'<select name="werJezykowa"></input>'
										+ '<option value="?">?</option>'
										+ '<option value="">Z UST. FILMU</option>'
										+ '<option value="napisy">Napisy</option>'
										+ '<option value="dubbing">Dubbing</option>'
										+ '<option value="lektor">Lektor</option>'
										+ '</select>'
					
					//Tworzenie przycisków "Zatwierdź" i "Edytuj" dla każdego filmu, gdzie name jest na zasadzie film_100 (dla filmu o id wydarzenia 100 w VT)
					var buttonZatwierdz = '<input name="film_' + value.id + '" type="button" value="Zatwierdź">';
					var buttonEdytuj = '<input name="edytuj_film_' + value.id + '" type="button" value="Edytuj">';
					
					
					//Generowanie wiersza w tabeli
					outTable += '<tr><td>' +value.id +' '+ value.tytul + '</td><td>' + wybierzFilmWWW +'</td><td>'+idFilmu+'</td><td>'+ format2d3d +'</td><td>'+ werJezykowa +'</td><td>' + buttonZatwierdz + ' ' +buttonEdytuj+ '</td></tr>';
				});//filmy.each(function()

				$('#filmy').append(outTable);

				//ukrycie przycisków Edytuj dla wszystkich wierszy
				$('#filmy input[name^="edytuj_film_"]').hide();

				
				$('.id_filmu').prop('readonly', true).focus(function(){
				//nadanie polu .id_filmu właściwości readonly i odabranie możliwości focusu na nim (pole jest zupełnie nieaktywne dla użytkownika)
				//w pole to skrypt będzie sam wpisywał id filmu (z www)
					$('.id_filmu').blur();
				})
				
				//dodanie do pola wyboru filmu (z wpisanych na www) autocomplete z danymi pobranymi ze strony (zmienna filmyWWW generowana w php i przekazywana przez JSON)
				//zmiena ta przekazuje informacje o wpisanych na stronę filmach w postaci tablicy, w której każdy element [film] ma label [tytuł filmu] i value[id filmu z WWW]
				//funkcja obsługi zdarzenia select powoduje, że wyświetlany jest tytuł, a do inputa .id_filmu przekazany jest identyfikator filmu

				$('.wybierzFilmWWW').autocomplete({ 
				//dodanie autocomplete do każdego pola wyboru tytułu filmu w tabeli #filmy
													source: filmyWWW,
													select: function( event, ui ) { 

														//wymuszenie wyświetlenia tytułu filmu zamiast id w polu .wybierzFilmWWW
														event.preventDefault();
														$(this).val(ui.item.label);
														//wypełnienie pola id_filmu identyfikatorem filmu z www
														$(this).parents('tr').find('.id_filmu').val(ui.item.value);

													},
				});

				$('.wybierzFilmWWW').focus(function(){
				//dodanie obsługi zdarzeń focus dla pól tabeli #filmy
				//w związku z tym, że walidacja pół wiersza tej tabeli może nadać poszczególnym polom (a raczej ich rodzicom <td>) klasę .bladDanych
				//klasa ta jest zdejmowna z pola po jego uaktywnieniu (focus)
				//pole identyfikatora filmu nie może być uaktywnione, dlatego zdejmowana jest z niego klasa .bladDanych przy uaktywnieniu pola tytułu

					$(this).parent().removeClass('bladDanych');
					$(this).parents('tr').find('.id_filmu').parent().removeClass('bladDanych');
				});

				//j.w. - zdjęcie klasy .bladDanych z pół format2d3d i werJezykowa przy ich uaktywnieniu
				$('select[name="format2d3d"]').focus(function(){	$(this).parent().removeClass('bladDanych');	});
				$('select[name="werJezykowa"]').focus(function(){	$(this).parent().removeClass('bladDanych');	});

				$('#filmy input[name^="film_"]').click(function(){
				//dodanie obsługi zdarzenia na przycisk "Zatwierdź" w wierszu
				//przyciski "Zatwierdź" identyfikowane są na podstawie początku nazwy "film_" (bo każdy przycisk ma nazwę w konwencji film_IDzVT)
				//waliduje poprawność danych w tym wierszu i przypisuje dane filmu do jego projekcji w tabeli projekcji

					//POBRANIE PÓL A NIE SAMYCH WARTOŚCI!!!
					var $wybranyFilmTytul = $(this).parents('tr').find('.wybierzFilmWWW');
					var $wybranyFilmID = $(this).parents('tr').find('.id_filmu');
					var $format2d3d = 	$(this).parents('tr').find('[name="format2d3d"]');
					var $werJezykowa = 	$(this).parents('tr').find('[name="werJezykowa"]');

					//WALIDACJA WIERSZA FORMULARZA (dla którego kliknięto "Zatwierdź")
					
					//Sprawdzenie, czy można zatwierdzić wiersz tabeli, określa to zmienna czyZatwierdzic
					var czyZatwierdzic = true;

					if($wybranyFilmID.val() != ''){
					//sprawdzenie czy wybrany identyfikator filmu nie różni się od wpisanego tytułu filmu
					//(będzie się różnił w sytuacji, gdy zmieniono tekst pola tytułu bez użycia opcji a autocomplete)
					//jeśli się różni następuje poprawienie wybranego tytułu filmu na zgodny z identyfikatorem
					//nie zostaje też automatycznie uruchomiona akcja
					//sprawdzane oczywiście tylko, jeśli został ustawiony jakiś identyfikator filmu przy użyciu autocomplete

						//funkcja zwraca do tablicy arr objekty tych filmów, dla których value równa się pobranemu identyfikatorowi (będzie to zawsze tylko jeden film)
						arr = $.grep(filmyWWW, function( a ) {
							return a.value == $wybranyFilmID.val();
						});
						//przypisanie do pola tytułu pobranego tytułu filmu (arr[0] bo zawsze będzie tylko jeden taki film), jeśli się różnią
						if($wybranyFilmTytul.val() != arr[0].label){

							$wybranyFilmTytul.val(arr[0].label);
							czyZatwierdzic = false;
							//zabranie klasy .bladDanych z pola tytułu
							$wybranyFilmTytul.parent().removeClass('bladDanych');
						}
					}//if($wybranyFilmID.val() =='')
					
					if($wybranyFilmTytul.val() =='' || $wybranyFilmID.val() =='' || $format2d3d.val() == '?' || $werJezykowa.val() == '?'){
					//sprawdzenie, czy podano wszystkie wartości w zatwierdzanym wierszu - jeśli nie, zatrzymuje możliwość zatwierdzenia wiersza
						czyZatwierdzic = false;

						//sprawdzenie poszczególnych pól - jeśli nie podano wartości albo wybrano ? - oznaczenie komórki jako błędnej za pomocą klasy bladDanych
						if($wybranyFilmTytul.val() ==''){ 	$wybranyFilmTytul.parent().addClass('bladDanych'); }
						if($wybranyFilmID.val() ==''){ 	$wybranyFilmID.parent().addClass('bladDanych'); }
						if($format2d3d.val() == '?'){	$format2d3d.parent().addClass('bladDanych');	}
						if($werJezykowa.val() == '?'){	$werJezykowa.parent().addClass('bladDanych');	}
					}

					if(czyZatwierdzic == true){
					//zatwierdzanie ustawień filmu w danym wierszu
					//blokuje możliwość dalszej edycji tego wiersza (można odblokować przyciskiem 'Edytuj')
					//przepisuje wybrane ustawienia do odpowiednich pól (dla danego filmu) w tabeli projekcji (#dodajProjekcje)

						//ukrycie przycisku 'Zatwierdź' dla zatwierdzonego wiersza i wyświetlenie 'Edytuj'
						//$(this) to w tej funkcji właśnie przycisk 'Zatwierdź' a $(this).next() to 'Edytuj'
						$(this).hide();
						$(this).next().show();

						//zablokowanie wszystkich pól wiersza
						$wybranyFilmTytul.prop( "disabled", true );
						$wybranyFilmID.prop( "disabled", true );
						$format2d3d.prop( "disabled", true );
						$werJezykowa.prop( "disabled", true );

						//zmiana wyglądu całego wiersza - poprzez nadanie klasy
						$(this).parents('tr').addClass('zablokowane');

						var film_tr_class = $(this).attr('name');
						//wypełnienie danych filmu w tabeli projekcji (na podstwie wybranych wartości w tabeli #filmy) i zmiana wyświetlania za pomocą dodania klasy
						//każda projekcja danego filmu (a w zasadzie wiersz <tr>) ma klasę identyczną z wartością atrybutu 'name' przycisku "Zatwierdź" (przypisywana do film_tr_class)
						//- w ten sposób łatwo wybrać na raz wszystkie projekcje danego filmu za pomocą $('#dodajProjekcje .'+film_tr_class)
						$('#dodajProjekcje .'+film_tr_class).addClass('wypelnione'); 
						//Wpisanie tytułu filmu (z WWW)
						$('#dodajProjekcje .'+film_tr_class).find('.film').append().text($wybranyFilmTytul.val());
						//Wpisanie id filmu (z WWW) do pola hidden
						$('#dodajProjekcje .'+film_tr_class).find('.film_projekcji').val($wybranyFilmID.val());
						//wpisanie formatu (2d3d)
						$('#dodajProjekcje .'+film_tr_class).find('.format').val($format2d3d.val()); //jeśli wybrano format 2D to zostaje puste
						//Wpisanie wersji językowej 
						$('#dodajProjekcje .'+film_tr_class).find('.wersja_jezykowa').val($werJezykowa.val());
						//Ustawienie statusu na www  na 'Gotowy do zapisu'
						$('#dodajProjekcje .'+film_tr_class).find('.status_www').append().text('Gotowe do zapisu');


					}
				});

				$('#filmy input[name^="edytuj_film_"]').click(function(){
				//dodanie obsługi zdarzenia na przycisk "Edytuj" w wierszu
				//przyciski "Edytuj" identyfikowane są na podstawie początku nazwy "edytuj_film_" (bo każdy przycisk ma nazwę w konwencji film_IDzVT)
				//odblokowuje możliwość edycji w wierszu tabeli #filmy

					//ukrycie przycisku 'Zatwierdź' dla zatwierdzonego wiersza i wyświetlenie 'Edytuj'
					//$(this) to w tej funkcji właśnie przycisk 'Edytuj' a $(this).prev() to 'Zatwierdź'
					$(this).hide();
					$(this).prev().show();

					//odblokowanie wszystkich pól wiersza
					$(this).parents('tr').find('.wybierzFilmWWW').prop( "disabled", false );
					$(this).parents('tr').find('.id_filmu').prop( "disabled", false );
					$(this).parents('tr').find('[name="format2d3d"]').prop( "disabled", false );
					$(this).parents('tr').find('[name="werJezykowa"]').prop( "disabled", false );

					//zmiana wyglądu całego wiersza - poprzez nadanie klasy
					$(this).parents('tr').removeClass('zablokowane');

					var film_tr_class = $(this).prev().attr('name');

					$('#dodajProjekcje .'+film_tr_class).removeClass('wypelnione'); 
					//Wyczyszczenie tytułu filmu
					$('#dodajProjekcje .'+film_tr_class).find('.film').append().text('[nie wybrano]');
					//Wyczyszczenie identyfikatora filmu w polu hidden
					$('#dodajProjekcje .'+film_tr_class).find('.film_projekcji').val('');
					//Wyczyszczenie formatu (2d3d)
					$('#dodajProjekcje .'+film_tr_class).find('.format').val('');
					//Wyczyszczenie wersji językowej 
					$('#dodajProjekcje .'+film_tr_class).find('.wersja_jezykowa').val('');

					//Ustawienie statusu na www  na 'Nie zapisano'
					$('#dodajProjekcje .'+film_tr_class).find('.status_www').append().text('Niezapisane');
				});


					  
		},
		error: function(jqXHR, exception){
		//jeśli wystąpił błąd wczytania danych z Systemu biletowego wyświetlana jest informacja w konsoli oraz wyświetlane okno
			console.log("Nie udało się poprawnie przeczytać XML");

			 if (jqXHR.status === 0) {
                console.log('Not connect.\n Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found. [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (exception === 'parsererror') {
                console.log('Requested XML parse failed.');
            } else if (exception === 'timeout') {
                console.log('Time out error.');
            } else if (exception === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error.\n' + jqXHR.responseText);
            }

			$('#wait').dialog('close');
			$('#error').dialog('show');
		},
		complete: function(){
		//gdy zakończono pobieranie danych ukrywane jest okno #wait i można wtedy korzystać ze strony
			$('#wait').dialog('close');
		}
	});

}//wczytajProjekcjeVT()


function idObjInArray(obj, arr, polePorownania){
//funkcja sprawdzająca, czy w tablicy obiektów arr znajduje się taki, dla którego wartość zadanego pola (polePorownania)
//jest identyczna z wartością tego pola w obiekcie obj
//jeśli znajdzie taki obiekt w tablicy zwraca true
	var i;
	for (i=0; i < arr.length; i++){
		if(arr[i][polePorownania] == obj[polePorownania]){
			return true;
		}
	}
	return false;
}//function objInArray(obj, arr)

function pobierzDate(data, format){
//funkcja poobiera datę w formacie Date() i zwraca jako string w formacie yyyy-mm-dd lub dd-mm-yyyy
//gdy w miesiąciu lub dniu nie ma początkowego 0 (np. 2015-1-1) to go dodaje i zwraca 2015-01-01
//gdy format == 'w,dd mt yyyy' zwraca np. Poniedziałek, 26 listopada 2015
var miesiaceOdmienione = ['stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'];

//zawiera dni tygodnia 0 - niedziela, 6 - sobota - zgodnie z formatem dla funkcji date() 'w'
var dniTygodnia = ['Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota'];

	var rok = data.getFullYear();
	var miesiac = data.getMonth()+1;
	if(miesiac < 10){
		miesiac = '0'+ miesiac;
	}
	var dzien = data.getDate();
	if(dzien < 10){
		dzien = '0' + dzien;
	}

	if(format == 'yyyy-mm-dd'){
		return rok + '-' + miesiac + '-' + dzien;
	}
	if(format == 'dd-mm-yy'){
		return dzien + '-' + miesiac + '-' + rok;
	}
	if(format == 'w,dd mt yyyy'){
		return dniTygodnia[data.getDay()]+', '+data.getDate()+' '+miesiaceOdmienione[data.getMonth()]+' '+rok;
	}

	//jeśli nie podano znanego formatu
	return false;
}

function pobierzCzas(dataCzas){
//funkcja poobiera datęCzas w formacie Date() i zwraca jako string w formacie hh:mm
	var godzina = dataCzas.getHours();
	if(godzina < 10){
		godzina = '0' + godzina;
	}
	var minuta = dataCzas.getMinutes();
	if(minuta < 10){
		minuta = '0' + minuta;
	}
	return godzina + ':' + minuta;
}//function pobierzCzas(dataCzas)




