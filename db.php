<?php
// db.php: Archivo de conexión a la base de datos
$host = 'localhost';  // Cambia esto si es necesario
$dbname = 'Steren';   // Nombre de la base de datos
$username = 'root';   // Usuario de MySQL
$password = '';       // Contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
