<?php
$host = 'localhost';
$dbname = 'supd5886_todo';
$username = 'supd5886_Lamian';  // Change this to your MySQL username
$password = 'Lamian12';      // Change this to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>