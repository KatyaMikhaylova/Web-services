<?php
if (isset($_POST['start'])) {

    $start=$_POST['start'];
    $end=$_POST['end'];
    $start = str_replace(' ', '%20', $start);
    $end = str_replace(' ', '%20', $end);
  $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $start .
"&destination=" . $end . "&key=AIzaSyCaFSoSsq6HeKfH9GR_KlwA14aDaytyEu0";

  echo json_encode(file_get_contents($url));
}

?>