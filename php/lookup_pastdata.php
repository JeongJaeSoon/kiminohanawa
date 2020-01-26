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

$request_data = json_decode(file_get_contents("php://input"), TRUE);

$year = (int)$request_data['year'];
$months = (int)$request_data['months'];
$weeks = (int)$request_data['weeks'];

$firstDay = $request_data['year']."-".$request_data['months']."-01";
$dateDifference = 6 - date('w', strtotime($firstDay));

$weekDay_Start;
$weekDay_End;

if ($weeks === 1) {
    //첫 주차 때 일요일이 시작이 아니라면 일요일부터 시작하게끔 만듬
    $weekDay_Start = strtotime($firstDay) - (6 - $dateDifference) * 86400;
    $weekDay_End = strtotime($firstDay) + $dateDifference * 86400;
}
else if ($weeks >= 2) {
    $weekDay_Start = strtotime($firstDay) + ($dateDifference + 7 * ($weeks - 1) - 6) * 86400;
    $weekDay_End = strtotime($firstDay) + ($dateDifference + 7 * ($weeks - 1)) * 86400;
}

$response_data[][] = array();

for ($i=0; $i < 7; $i++) { 
    
    $findDay  = $weekDay_Start + 86400 * $i;
    $timeSet  = date(("Y-m-d"), $findDay);

    $timeDay_Start  = strtotime($timeSet.' 00:00:00');
    $timeDay_End    = strtotime($timeSet.' 23:59:59');

    $setTime_Start  = date("Y-m-d H:i:s", $timeDay_Start);
    $setTime_End    = date("Y-m-d H:i:s", $timeDay_End);

    $sql = "SELECT MAX(co2), MIN(co2), MAX(ultrafine_dust), MIN(ultrafine_dust), MAX(fine_dust), MIN(fine_dust)
        FROM dummy_data
        -- FROM measure_data
        WHERE date_time     >= '$setTime_Start'
        AND date_time       <= '$setTime_End'";

    $result   = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_row($result)) {
        $response_data[$i] = $row;
    }

    $response_data[$i][6] = $timeSet;
}

for ($i=0; $i < sizeof($response_data); $i++) { 
    for ($j=0; $j < sizeof($response_data[$i]) - 1; $j++) { 
        $response_data[$i][$j] = (int)$response_data[$i][$j];
    }
}

$output = json_encode($response_data);
print_r($output);
?>