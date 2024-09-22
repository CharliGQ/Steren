<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO productos (nombre, categoria, precio, stock, descripcion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nombre, $categoria, $precio, $stock, $descripcion])) {
        echo "Producto creado exitosamente.";
    } else {
        echo "Error al crear el producto.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Producto</title>
</head>
<body>
    <h2>Crear Producto</h2>
    <form method="POST">
        Nombre: <input type="text" name="nombre" required><br><br>
        Categoría: <input type="text" name="categoria" required><br><br>
        Precio: <input type="number" step="0.01" name="precio" required><br><br>
        Stock: <input type="number" name="stock" required><br><br>
        Descripción: <textarea name="descripcion"></textarea><br><br>
        <input type="submit" value="Crear Producto">
    </form>
</body>
</html>
