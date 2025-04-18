<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Weather App</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Weather Checker 🌤️</h1>

  <form action="weather.php" method="get" autocomplete="off">
  <div class="autocomplete-wrapper">
    <input type="text" id="cityInput" name="city" placeholder="Enter city name" required>
    <ul id="suggestions"></ul>
  </div>

  <select name="unit">
    <option value="metric">Celsius (°C)</option>
    <option value="imperial">Fahrenheit (°F)</option>
  </select>

  <button type="submit">Get Weather</button>
</form>

<script>
  const input = document.getElementById('cityInput');
  const suggestions = document.getElementById('suggestions');
  const apiKey = '65c773aa40850293c20997299c7a903c'; // Replace with your OpenWeatherMap key

  input.addEventListener('input', async () => {
    const query = input.value;
    suggestions.innerHTML = '';

    if (query.length < 3) return;

    const url = `https://api.openweathermap.org/geo/1.0/direct?q=${query},US&limit=5&appid=${apiKey}`;
    const res = await fetch(url);
    const data = await res.json();

    if (data.length === 0) return;

    data.forEach(place => {
      const li = document.createElement('li');
      li.textContent = `${place.name}, ${place.state || ''}, ${place.country}`;
      li.addEventListener('click', () => {
        input.value = `${place.name}, ${place.state || place.country}`;
        suggestions.innerHTML = '';
      });
      suggestions.appendChild(li);
    });
  });

  document.addEventListener('click', (e) => {
    if (!e.target.closest('.autocomplete-wrapper')) {
      suggestions.innerHTML = '';
    }
  });
</script>

<?php
    if (isset($_SESSION['weather_result'])) {
      echo $_SESSION['weather_result'];
      unset($_SESSION['weather_result']);
    }

    if (isset($_SESSION['search_history']) && count($_SESSION['search_history']) > 0) {
      echo "<div class='history'><h3>Recent Searches</h3><ul>";
      foreach (array_reverse($_SESSION['search_history']) as $city) {
        echo "<li>$city</li>";
      }
      echo "</ul></div>";
    }
  ?>
  
</body>
</html>





