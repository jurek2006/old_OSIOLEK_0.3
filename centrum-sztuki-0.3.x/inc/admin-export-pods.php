<h1>Export PODS</h1>

<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">

<?php 

    if(isset( $_POST["exp_filmy"])){
    //Jeśli kliknięto przycisk "Eksportuj filmy"

        $params = array(
            'limit' => -1
        );
        $pods = pods( PODS_FILMY , $params );
        $all_data = $pods->export_data();
        if ( empty( $all_data ) ) {
        // jeśli nie ma  żadnych znalezionych danych w pods, a więc $all_data jest empty
            exit('Nie znaleziono żadnych filmów');
        }
        else {
        // jeśli znaleziono filmy i są w tablicy $all_data

            $all_data['rodzaj_danych'] = 'filmy'; //dodanie pola na rodzaj danych, żeby przy imporcie można było sprawdzić czy to są te dane, które powinny być

            $all_data_json = json_encode($all_data);
            
        }

        $home_url_json = trim (json_encode(home_url()), '"' ); //zakodowanie home_url w json (z pozbyciem się średników na początku i końcu) - na potrzeby wyszukiwania i podmiany ścieżek

        $all_data_json = str_replace($home_url_json, HOME_URL_ALIAS, $all_data_json); //zamiana ścieżek zawierających home_url na string zdefiniowany w HOME_URL_ALIAS

        echo sprintf('<textarea name="textarea" rows="10"  style="width: 100%%">%s</textarea>', $all_data_json); //wstawienie danych zakodowanych w json do textarea

        

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