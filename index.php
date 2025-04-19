<?php
$city = $_GET['city'] ?? '';
$unit = $_GET['unit'] ?? 'imperial'; // Default to Fahrenheit

$temperature = '';
$condition = '';
$description = '';
$icon = '';
$cityName = '';

if ($city) {
    $apiKey = "65c773aa40850293c20997299c7a903c"; // Replace with your real OpenWeatherMap API key
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=$apiKey&units=$unit";

    $response = @file_get_contents($apiUrl);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['main']['temp'])) {
            $temperature = $data['main']['temp'];
            $condition = strtolower($data['weather'][0]['main']);
            $description = strtolower($data['weather'][0]['description']);
            $cityName = $data['name'];

            // Icon mapping
            $iconMap = [
                'clear sky' => 'clear-day',
                'few clouds' => 'partly-cloudy-day',
                'scattered clouds' => 'cloudy',
                'broken clouds' => 'overcast',
                'shower rain' => 'rain',
                'rain' => 'rain',
                'thunderstorm' => 'thunderstorms',
                'snow' => 'snow',
                'mist' => 'mist',
                'clouds' => 'cloudy',
            ];

            $iconFileName = $iconMap[$description] ?? 'cloudy';
            $icon = "icons/" . $iconFileName . ".png";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Weather Checker</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Weather Checker ⛅</h1>

    <form action="" method="get">
      <input type="text" name="city" placeholder="Enter city name" value="<?php echo htmlspecialchars($city); ?>" required>
      <select name="unit">
        <option value="metric" <?php echo $unit === 'metric' ? 'selected' : ''; ?>>Celsius (°C)</option>
        <option value="imperial" <?php echo $unit === 'imperial' ? 'selected' : ''; ?>>Fahrenheit (°F)</option>
      </select>
      <button type="submit">Get Weather</button>
    </form>

    <?php if ($temperature && $icon): ?>
      <div class="weather-box">
        <h3><?php echo htmlspecialchars($city); ?></h3>
        <img src="<?php echo $icon; ?>" alt="<?php echo $desc; ?>" class="weather-icon">
        <p style="text-transform: capitalize;"><?php echo htmlspecialchars($description); ?></p>
        <p><strong>Temperature:</strong> <?php echo round($temperature, 2); ?>° <?php echo $unit === 'metric' ? 'C' : 'F'; ?></p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
