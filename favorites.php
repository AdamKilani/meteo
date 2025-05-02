<?php
// favorites.php
session_start();
require_once 'lang/' . ($_SESSION['lang'] ?? 'fr') . '.php';
require_once 'db/Database.php';
require_once 'db/WeatherLocation.php';

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = $lang['error_csrf'];
    header('Location: index.php');
    exit;
}

$db = new Database();
$weatherLocationObj = new WeatherLocation($db->getConnection());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Ajouter un favori
    if ($_POST['action'] === 'add' && isset($_POST['location'])) {
        $location = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
        $notes = isset($_POST['notes']) ? filter_var(trim($_POST['notes']), FILTER_SANITIZE_STRING) : '';

        if (!empty($location)) {
            $result = $weatherLocationObj->addFavorite($location, $notes);
            $_SESSION['message'] = $result ? $lang['favorite_added'] : $lang['error_adding_favorite'];
        } else {
            $_SESSION['error'] = $lang['error_empty_location'];
        }
    }
    // Supprimer un favori
    elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id !== false) {
            $result = $weatherLocationObj->deleteFavorite($id);
            $_SESSION['message'] = $result ? $lang['favorite_deleted'] : $lang['error_deleting_favorite'];
        } else {
            $_SESSION['error'] = $lang['error_invalid_id'];
        }
    }
}

header('Location: index.php');
exit;