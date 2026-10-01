<?php
// db.php - Database connection using PDO

$host    = 'localhost';
$dbname  = 'ultimez_interview_tasks';
$user    = 'root';   // XAMPP default
$pass    = '';       // XAMPP default is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Show a friendly message (don't expose passwords/details to visitors)
    http_response_code(500);
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"></head>'
      . '<body><div class="container"><div class="error">'
      . 'Could not connect to the database. Please check that MySQL is running '
      . 'and the settings in <code>db.php</code> are correct.'
      . '</div></div></body></html>');
}
