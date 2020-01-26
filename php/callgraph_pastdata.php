<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
date_default_timezone_set('Asia/Seoul');

$conn = mysqli_connect(
     '13.124.189.186',
     'root',
     'bok2019',
     'kiminohanawa',
     3306
);

$request_data  = json_decode(file_get_contents("php://input"), TRUE);

$findDay = $request_data['day'];

$response_data[][] = array();

for ($i=0; $i < 24; $i++) { 
     
     $hour = strval($i);

     $timeDay_Start  = strtotime($findDay.' '.$hour.':00:00');
     $timeDay_End    = strtotime($findDay.' '.$hour.':59:59');

     $setTime_Start  = date("Y-m-d H:i:s", $timeDay_Start);
     $setTime_End    = date("Y-m-d H:i:s", $timeDay_End);

     $sql = "SELECT AVG(co2), AVG(ultrafine_dust), AVG(fine_dust)
          FROM dummy_data
          -- FROM measure_data
          WHERE date_time     >= '$setTime_Start'
          AND date_time       <= '$setTime_End'";

     $result = mysqli_query($conn, $sql);

     while ($row = mysqli_fetch_row($result)) {
          $response_data[$i] = $row;
     }
}

for ($i=0; $i < sizeof($response_data); $i++) { 
     for ($j=0; $j < sizeof($response_data[$i]); $j++) { 
          $response_data[$i][$j] = (int)$response_data[$i][$j];
     }
}

$output = json_encode($response_data);
print_r($output);

?>