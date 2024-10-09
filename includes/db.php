<?php
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';  // Change this to your MySQL username
$password = '';      // Change this to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>