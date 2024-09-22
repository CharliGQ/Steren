<?php
include 'db.php';

$sql = "SELECT * FROM productos";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Productos</title>
</head>
<body>
    <h2>Lista de Productos</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($productos as $producto) { ?>
        <tr>
            <td><?php echo $producto['id']; ?></td>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['categoria']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['stock']; ?></td>
            <td><?php echo $producto['descripcion']; ?></td>
            <td>
                <a href="update.php?id=<?php echo $producto['id']; ?>">Editar</a> |
                <a href="delete.php?id=<?php echo $producto['id']; ?>" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
