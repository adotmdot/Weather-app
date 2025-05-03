<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//API key from OpenWeatherMap
$apiKey = '65c773aa40850293c20997299c7a903c'; 

//input values
$city = isset($_GET['city']) ? urlencode($_GET['city']) : '';
$unit = isset($_GET['unit']) ? $_GET['unit'] : 'metric';

if ($city) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units={$unit}&appid={$apiKey}";

    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'City not found or API error.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No city provided.'
    ]);
}
?>
