<?php
// index.php
session_start();

// Définir des constantes pour les chemins
define('BASE_PATH', __DIR__);
define('LANG_PATH', BASE_PATH . '/lang/');
define('DB_PATH', BASE_PATH . '/db/');

// Gestion de la langue
$_SESSION['lang'] = $_SESSION['lang'] ?? 'fr';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Générer un token CSRF
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

require_once LANG_PATH . $_SESSION['lang'] . '.php';
require_once DB_PATH . 'Database.php';
require_once DB_PATH . 'WeatherLocation.php';

// Initialiser la base de données
$db = new Database();
$weatherLocationObj = new WeatherLocation($db->getConnection());

// Récupérer les favoris
$favorites = $weatherLocationObj->getFavorites();
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lang['page_title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($lang['site_name']) ?></h1>
        <nav aria-label="Language selector">
            <div class="language-selector">
                <a href="?lang=fr" class="<?= $_SESSION['lang'] === 'fr' ? 'active' : '' ?>" aria-current="<?= $_SESSION['lang'] === 'fr' ? 'true' : 'false' ?>">Français</a>
                <a href="?lang=en" class="<?= $_SESSION['lang'] === 'en' ? 'active' : '' ?>" aria-current="<?= $_SESSION['lang'] === 'en' ? 'true' : 'false' ?>">English</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success" role="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error" role="alert"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <section class="main-weather" id="current-weather" aria-labelledby="weather-heading">
            <h2 id="weather-heading"><?= htmlspecialchars($lang['current_weather']) ?></h2>
            <div id="weather-data">
                <p><?= htmlspecialchars($lang['search_prompt']) ?></p>
            </div>
        </section>

        <section class="search-section" aria-labelledby="search-heading">
            <h2 id="search-heading"><?= htmlspecialchars($lang['search_location']) ?></h2>
            <form id="search-form" class="search-form" action="search.php" method="GET" aria-label="Search weather by location">
                <input type="text" id="location" name="location" placeholder="<?= htmlspecialchars($lang['search_placeholder']) ?>" required aria-required="true">
                <button type="submit" aria-label="<?= htmlspecialchars($lang['search_button']) ?>"><?= htmlspecialchars($lang['search_button']) ?></button>
            </form>
        </section>

        <section class="favorites-section" aria-labelledby="favorites-heading">
            <h2 id="favorites-heading"><?= htmlspecialchars($lang['favorites']) ?></h2>
            <div id="favorites-container">
                <?php if (count($favorites) > 0): ?>
                    <ul class="favorites-list" aria-label="List of favorite locations">
                        <?php foreach ($favorites as $favorite): ?>
                            <li data-location="<?= htmlspecialchars($favorite['location']) ?>">
                                <span><?= htmlspecialchars($favorite['location']) ?></span>
                                <button class="view-btn" aria-label="<?= htmlspecialchars($lang['view']) . ' ' . htmlspecialchars($favorite['location']) ?> weather"><?= htmlspecialchars($lang['view']) ?></button>
                                <form action="favorites.php" method="POST" class="delete-form" aria-label="Delete favorite location">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($favorite['id']) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="delete-btn" aria-label="<?= htmlspecialchars($lang['delete']) . ' ' . htmlspecialchars($favorite['location']) ?> from favorites"><?= htmlspecialchars($lang['delete']) ?></button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?= htmlspecialchars($lang['no_favorites']) ?></p>
                <?php endif; ?>
            </div>

            <div class="add-favorite" aria-labelledby="add-favorite-heading">
                <h3 id="add-favorite-heading"><?= htmlspecialchars($lang['add_favorite']) ?></h3>
                <form id="add-favorite-form" action="favorites.php" method="POST" aria-label="Add new favorite location">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="form-group">
                        <label for="favorite-location"><?= htmlspecialchars($lang['location']) ?></label>
                        <input type="text" id="favorite-location" name="location" required aria-required="true">
                    </div>
                    <div class="form-group">
                        <label for="notes"><?= htmlspecialchars($lang['notes']) ?></label>
                        <textarea id="notes" name="notes" aria-describedby="notes-description"></textarea>
                        <p id="notes-description" class="visually-hidden"><?= htmlspecialchars($lang['notes']) ?> are optional</p>
                    </div>
                    <button type="submit" aria-label="<?= htmlspecialchars($lang['add']) ?> favorite location"><?= htmlspecialchars($lang['add']) ?></button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p><?= htmlspecialchars($lang['footer_text']) ?> &copy; 2025</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>