<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
date_default_timezone_set('Asia/Seoul');

$conn = mysqli_connect(
    '13.124.189.186',
    'root',
    'bok2019',
    'kiminohanawa',
    3306
);

$response_data[][] = array();

for ($i=0; $i < 6; $i++) { 
    $timeSet = strtotime("-$i hours");

    $timeStamp_Real = date("Y-m-d H:i", $timeSet).':00';
    $timeStamp_Start = date("Y-m-d H", $timeSet).':00:00';
    $timeStamp_End = date("Y-m-d H", $timeSet).':59:59';
    
    $response_time;

    if ($i === 0) {
        // var_dump($timeStamp_Real);
        $sql = "SELECT co2, ultrafine_dust, fine_dust
                FROM dummy_data
                -- FROM measure_data
                WHERE date_time = '$timeStamp_Real'";
        $response_time = $timeStamp_Real;
    }
    else {
        // var_dump($timeStamp_Start);
        // var_dump($timeStamp_End);
        $sql = "SELECT AVG(co2), AVG(ultrafine_dust), AVG(fine_dust)
                FROM dummy_data
                -- FROM measure_data
                WHERE date_time >= '$timeStamp_Start'
                AND date_time <= '$timeStamp_End'";
        $response_time = $timeStamp_Start;
    }

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_row($result)) {
        $response_data[$i] = $row;
    }

    $response_data[$i][3] = $response_time;
}

$response_data_tmp[][] = array();

for ($i=0; $i < sizeof($response_data); $i++) { 

    $tmp_index = sizeof($response_data) - $i - 1;

    for ($j=0; $j < sizeof($response_data[$i]); $j++) { 
        $response_data_tmp[$i][$j] = $response_data[$tmp_index][$j];
    }
}

for ($i=0; $i < sizeof($response_data); $i++) { 

    $tmp_index = sizeof($response_data) - $i - 1;

    for ($j=0; $j < sizeof($response_data[$i]); $j++) { 
        $response_data_tmp[$i][$j] = $response_data[$tmp_index][$j];
    }
}

for ($i=0; $i < sizeof($response_data); $i++) { 
    for ($j=0; $j < sizeof($response_data[$i]); $j++) { 
        if ($j === sizeof($response_data[$i]) - 1) {
            $response_data[$i][$j] = $response_data_tmp[$i][$j];
        }
        else {
            $response_data[$i][$j] = (int)$response_data_tmp[$i][$j];
        }
    }
}

$output = json_encode($response_data);
print_r($output);
?>