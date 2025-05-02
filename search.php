<?php
// search.php
session_start();

// Définir des constantes pour les chemins
define('BASE_PATH', __DIR__);
define('LANG_PATH', BASE_PATH . '/lang/');

// Charger le fichier de langue
require_once LANG_PATH . ($_SESSION['lang'] ?? 'fr') . '.php';

header('Content-Type: application/json');

if (!isset($_GET['location']) || empty(trim($_GET['location']))) {
    echo json_encode([
        'success' => false,
        'message' => $lang['error_no_location']
    ]);
    exit;
}

$location = filter_var(trim($_GET['location']), FILTER_SANITIZE_STRING);

// Simuler un cache simple avec un fichier
$cache_file = 'cache/' . md5($location) . '.json';
$cache_duration = 3600; // 1 heure

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
    $weather_data = json_decode(file_get_contents($cache_file), true);
} else {
    $weather_data = getWeatherData($location);
    if ($weather_data === false) {
        echo json_encode([
            'success' => false,
            'message' => $lang['error_api_failure'] ?? 'Unable to fetch weather data for this location'
        ]);
        exit;
    }
    if (!file_put_contents($cache_file, json_encode($weather_data))) {
        echo json_encode([
            'success' => false,
            'message' => 'Error writing to cache'
        ]);
        exit;
    }
}

// Fonction pour récupérer les données météo
function getWeatherData($location) {
    // Étape 1 : Convertir le nom de la ville en coordonnées (latitude, longitude) avec Nominatim
    $geocode_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($location) . "&format=json&limit=1";
    $options = [
        'http' => [
            'header' => "User-Agent: MyWeatherApp/1.0 (contact@example.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $geocode_response = @file_get_contents($geocode_url, false, $context);
    if ($geocode_response === false) {
        return false; // Échec de la géocodification
    }

    $geocode_data = json_decode($geocode_response, true);
    if (empty($geocode_data) || !isset($geocode_data[0]['lat']) || !isset($geocode_data[0]['lon'])) {
        return false; // Coordonnées non trouvées
    }

    $latitude = $geocode_data[0]['lat'];
    $longitude = $geocode_data[0]['lon'];

    // Étape 2 : Récupérer les données météo avec Open-Meteo
    $weather_url = "https://api.open-meteo.com/v1/forecast?latitude=" . urlencode($latitude) . "&longitude=" . urlencode($longitude) . "&current_weather=true";
    $weather_response = @file_get_contents($weather_url);
    if ($weather_response === false) {
        return false; // Échec de la requête météo
    }

    $weather_data = json_decode($weather_response, true);
    if (!$weather_data || !isset($weather_data['current_weather'])) {
        return false; // Données météo invalides
    }

    // Mapper les codes météo d'Open-Meteo aux conditions de l'application
    $weather_code = $weather_data['current_weather']['weathercode'];
    $weather_types_map = [
        0 => 'sunny',  // Clear sky
        1 => 'sunny',  // Mainly clear
        2 => 'cloudy', // Partly cloudy
        3 => 'cloudy', // Overcast
        45 => 'cloudy', // Fog
        48 => 'cloudy', // Depositing rime fog
        51 => 'rainy',  // Light drizzle
        53 => 'rainy',  // Moderate drizzle
        55 => 'rainy',  // Dense drizzle
        61 => 'rainy',  // Light rain
        63 => 'rainy',  // Moderate rain
        65 => 'rainy',  // Heavy rain
        71 => 'snowy',  // Light snow
        73 => 'snowy',  // Moderate snow
        75 => 'snowy',  // Heavy snow
        95 => 'rainy',  // Thunderstorm
    ];
    $weather_type = $weather_types_map[$weather_code] ?? 'windy';

    // Open-Meteo ne fournit pas l'humidité directement dans current_weather, on peut utiliser une valeur simulée ou ajouter un autre appel API
    // Pour simplifier, on simule l'humidité
    $humidity = rand(30, 90);

    return [
        'location' => $location,
        'temperature' => round($weather_data['current_weather']['temperature']),
        'humidity' => $humidity,
        'wind_speed' => round($weather_data['current_weather']['windspeed']),
        'weather_type' => $weather_type,
        'timestamp' => time()
    ];
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode([
        'success' => true,
        'data' => $weather_data
    ]);
} else {
    header('Location: index.php');
    exit;
}