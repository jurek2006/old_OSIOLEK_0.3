<h1>Export - import PODS</h1>

<?php 
	$params = array(
        'limit' => -1
    );
    $pods = pods( 'filmy2' , $params );
    $all_data = $pods->export_data();
    if ( !empty( $all_data ) ) {
        // die(json_encode($all_data));
        print_r($all_data);

        foreach ($all_data as $key => $value) {
        	echo "<h2>\$all_data[$key]</h2>";
        	print_r($value);

	        $podsIMP = pods('filmy');
	        $ids = $podsIMP->import($value);
	        printf("Dodano: ".$ids);
        }
    }
    else {
    	printf("Nie ma filmów");
        // die(json_encode(array('error' => 'No filmy found.')));
    }
?>