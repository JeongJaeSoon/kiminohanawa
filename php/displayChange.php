<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
    // date_default_timezone_set('Asia/Seoul');

    // 홈페이지로 부터 display 변경 값 불러오기
    $request_data = json_decode(file_get_contents("php://input"), TRUE);
    $control = (int)$request_data['display'];

    // txt 파일 읽기
    $fp = fopen("remote.txt", "r");
    fclose($fp);
    // 여기서부터 쓰기 시작
    $fp = fopen("remote.txt", "w");
    fwrite($fp, $control);
    fclose($fp);
    
    $output = json_encode($control);
    echo "display 변경 => ".$control;
    
?>