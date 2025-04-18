<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = '65c773aa40850293c20997299c7a903c'; // ⬅️ Replace this with your actual OpenWeatherMap API key

$rawCity = htmlspecialchars($_GET['city']);
$unit = $_GET['unit'] ?? 'metric';
$unitSymbol = $unit === 'imperial' ? '°F' : '°C';

// 1. Use Geocoding API to get coordinates
$geoURL = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($rawCity) . "&limit=1&appid=$apiKey";
$geoResponse = file_get_contents($geoURL);
$geoData = json_decode($geoResponse, true);

if (!$geoData || count($geoData) === 0) {
    $_SESSION['weather_result'] = "<p class='error'>❌ Location not found. Try a valid city name (e.g., Dallas, TX).</p>";
    header("Location: index.php");
    exit();
}

$lat = $geoData[0]['lat'];
$lon = $geoData[0]['lon'];

// 2. Use coordinates to get weather
$weatherURL = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$apiKey&units=$unit";
$weatherResponse = file_get_contents($weatherURL);
$data = json_decode($weatherResponse, true);

$output = "";

if (!$data || $data['cod'] != 200) {
    $output = "<p class='error'>❌ Weather data not found. Please try again later.</p>";
} else {
    $icon = $data['weather'][0]['icon'];
    $weatherIcon = "https://openweathermap.org/img/wn/{$icon}@2x.png";
    $condition = ucwords($data['weather'][0]['description']);
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $wind = $data['wind']['speed'];
    $country = $data['sys']['country'];
    $cityName = $data['name'];

    $timezoneOffset = $data['timezone'];
    $localTime = gmdate("M d, Y - h:i A", time() + $timezoneOffset);

    $output = "
      <div class='weather-box'>
        <h2>$cityName, $country</h2>
        <p class='time'>🕒 $localTime</p>
        <img src='$weatherIcon' alt='Weather icon'>
        <p>🌡️ Temperature: <strong>$temperature$unitSymbol</strong></p>
        <p>☁️ Condition: $condition</p>
        <p>💧 Humidity: $humidity%</p>
        <p>💨 Wind: $wind m/s</p>
      </div>
    ";

    $_SESSION['search_history'][] = $rawCity;
    $_SESSION['search_history'] = array_unique($_SESSION['search_history']);
    $_SESSION['search_history'] = array_slice($_SESSION['search_history'], -5);
}

$_SESSION['weather_result'] = $output;
header("Location: index.php");
exit();
