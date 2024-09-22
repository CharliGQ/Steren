<?php
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$id])) {
    echo "Producto eliminado exitosamente.";
} else {
    echo "Error al eliminar el producto.";
}

header("Location: read.php"); // Redirige de nuevo a la lista
exit();
?>
