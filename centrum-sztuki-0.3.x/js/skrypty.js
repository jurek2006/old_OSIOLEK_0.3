var $ = jQuery.noConflict();


jQuery(document).ready(function(){
	mailAntySpam();
});

function mailAntySpam(){
// funkcje antyspamowe dla adresów email
	var rozdzielacz = '(małpka)'; 

	$('span.mail').each(function(){
	// funkcja zamieniająca każdy "zamaskowany" adres email snajdujący się w <span class="mail"> na działający odnośnik mailto
	// rozdzielacz (zamiennik dla @ jest definiowany powyżej w zmiennej rozdzielacz)
	// zamiana następuje tylko wtedy, gdy rozdzielacz jest znaleziony w adresie zamaskowanym (jeśli nie, nic nie jest robione)
		var adresZamaskowany = $(this).text();

		var adresBezMaski = adresZamaskowany.replace(rozdzielacz,'@'); //zamiana zdefiniowanego rozdzielacza na znak @
		if(adresBezMaski != adresZamaskowany){
			// jeśli adres został zmodyfikowano (czyli zamieniono rozdzielacz na @)
			// następuje wstawienie poprawnego odnośnika zamiast zamaskowanego
			$(this).replaceWith( '<a href="mailto:' + adresBezMaski +'">' + adresBezMaski + '</a>' );
		}
	})

}

