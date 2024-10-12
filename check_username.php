<?php
require_once 'includes/db.php';

if (isset($_POST['username'])) {
    $username = sanitize($_POST['username']);
    $user_id = $_SESSION['user_id']; 

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $user_id]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        echo 'taken'; // Username sudah digunakan
    } else {
        echo 'available'; // Username tersedia
    }
}
?>
