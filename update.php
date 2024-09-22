<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE productos SET nombre = ?, categoria = ?, precio = ?, stock = ?, descripcion = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nombre, $categoria, $precio, $stock, $descripcion, $id])) {
        echo "Producto actualizado exitosamente.";
    } else {
        echo "Error al actualizar el producto.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Producto</title>
</head>
<body>
    <h2>Editar Producto</h2>
    <form method="POST">
        Nombre: <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required><br><br>
        Categoría: <input type="text" name="categoria" value="<?php echo $producto['categoria']; ?>" required><br><br>
        Precio: <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required><br><br>
        Stock: <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required><br><br>
        Descripción: <textarea name="descripcion"><?php echo $producto['descripcion']; ?></textarea><br><br>
        <input type="submit" value="Actualizar Producto">
    </form>
</body>
</html>
