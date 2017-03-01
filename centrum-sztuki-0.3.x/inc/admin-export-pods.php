<h1>Export PODS</h1>

<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">

<?php 

    if(isset( $_POST["exp_filmy"])){
    //Jeśli kliknięto przycisk "Eksportuj filmy"
    //Pobierane są wszystkie filmy (z pods PODS_FILMY), których ostatnia modyfikacja nastąpiła najpóźniej w dniu określonym datą $datetime
    //Są one encodowane do JSON, a ścieżka home_url jest zamieniana na string określony w HOME_URL_ALIAS
    //Na końcu ten JSON wyświetlany jest w textarea

        // ustawienia daty - filtru które filmy mają być wyeksportowane (brana pod uwagę jest ostatnia modyfikacja)
        $datetime = new DateTime('2017-01-01');
        $termin_poczatek = $datetime->format('Y-m-d');

        $params = array(
            'limit' => -1,
            'where' => 'DATE( post_modified ) >= "'.$termin_poczatek.'"'
        );
        $pods = pods( PODS_FILMY , $params );

        $all_data = array(); //tablica na zapisanie wszystkich eksportowanych danych

        if ( $pods->total() > 0 ) {

            while ( $pods->fetch() ) {
                $tytul_filmu =  $pods->display('post_title');
                $id = $pods->field('id');
                $single_data = $pods->export();
                $single_data['projekcje'] = ''; //wyczyszczenie pola 'projekcje', które nie jest w żaden sposób potrzebne, a zaśmieca dużą ilością danych

                $all_data[$id] = $single_data;
                
            }//while ( $pods->fetch() )

            $all_data['rodzaj_danych'] = 'filmy'; //dodanie pola na rodzaj danych, żeby przy imporcie można było sprawdzić czy to są te dane, które powinny być
            $all_data_json = json_encode($all_data);
            $home_url_json = trim (json_encode(home_url()), '"' ); //zakodowanie home_url w json (z pozbyciem się średników na początku i końcu) - na potrzeby wyszukiwania i podmiany ścieżek

            $all_data_json = str_replace($home_url_json, HOME_URL_ALIAS, $all_data_json); //zamiana ścieżek zawierających home_url na string zdefiniowany w HOME_URL_ALIAS

            echo sprintf('<textarea name="textarea" rows="10"  style="width: 100%%">%s</textarea>', $all_data_json); //wstawienie danych zakodowanych w json do textare

        }//$pods = pods( PODS_FILMY , $params )
        else{
            echo 'Nie znaleziono żadnych filmów spełniających kryterium eksportu.';
        }

    }//if(isset( $_POST["exp_filmy"]))
    else{
    // Nie kliknięto żadnego przycisku
        ?>
            <input type="submit" name="exp_filmy" value="Eksportuj filmy" class="button" />
        <?php
    }
	
?>

</form>

<script type="text/javascript">
    $(".button").button();
</script>