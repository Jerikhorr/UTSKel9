<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($location) {
    header("Location: $location");
    exit;
}

function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}

function checkPassword($password) {
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $password);
}
?>