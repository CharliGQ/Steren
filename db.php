<?php
// db.php: Archivo de conexión a la base de datos
$host = 'localhost';// Nombre del servidor de MySQL
$dbname = 'Steren';// Nombre de la base de datos
$username = 'root';// Usuario de MySQL
$password = '';// Contraseña de MySQL


try { // Intenta conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { // Si falla, muestra un mensaje de error
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>