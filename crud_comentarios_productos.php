<?php
// Incluir la conexión a la base de datos
require_once 'db.php';

// Definir la acción (crear, leer, actualizar, eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Comentarios de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear comentario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $producto_id = $_POST['producto_id'];
                $cliente_id = $_POST['cliente_id'];
                $comentario = $_POST['comentario'];
                $valoracion = $_POST['valoracion'];

                // Validación básica
                if (empty($producto_id) || empty($cliente_id) || empty($comentario) || empty($valoracion)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO comentarios_productos (producto_id, cliente_id, comentario, valoracion) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$producto_id, $cliente_id, $comentario, $valoracion])) {
                            echo '<div class="alert alert-success">Comentario agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el comentario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Comentario</h2>
            <form method="POST" action="crud_comentarios_productos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">ID del Producto</label>
                    <input type="number" name="producto_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">ID del Cliente</label>
                    <input type="number" name="cliente_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario</label>
                    <textarea name="comentario" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="valoracion" class="form-label">Valoración (1-5)</label>
                    <input type="number" name="valoracion" class="form-control" min="1" max="5" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Comentario</button>
            </form>
            <br>
            <a href="crud_comentarios_productos.php" class="btn btn-secondary">Ver comentarios</a>
            <?php
            break;

        case 'read':
            // Leer comentarios
            $sql = "SELECT c.*, p.nombre AS nombre_producto, cl.nombre AS nombre_cliente 
                    FROM comentarios_productos c 
                    JOIN productos p ON c.producto_id = p.id 
                    JOIN clientes cl ON c.cliente_id = cl.id";
            $stmt = $pdo->query($sql);
            $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Comentarios de Productos</h2>
            <a href="crud_comentarios_productos.php?action=create" class="btn btn-success mb-3">Agregar Comentario</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID del Producto</th>
                    <th>Nombre del Producto</th>
                    <th>ID del Cliente</th>
                    <th>Nombre del Cliente</th>
                    <th>Comentario</th>
                    <th>Valoración</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($comentarios as $comentario) { ?>
                    <tr>
                        <td><?php echo $comentario['id']; ?></td>
                        <td><?php echo $comentario['producto_id']; ?></td>
                        <td><?php echo $comentario['nombre_producto']; ?></td>
                        <td><?php echo $comentario['cliente_id']; ?></td>
                        <td><?php echo $comentario['nombre_cliente']; ?></td>
                        <td><?php echo $comentario['comentario']; ?></td>
                        <td><?php echo $comentario['valoracion']; ?></td>
                        <td><?php echo $comentario['fecha_comentario']; ?></td>
                        <td>
                            <a href="crud_comentarios_productos.php?action=update&id=<?php echo $comentario['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_comentarios_productos.php?action=delete&id=<?php echo $comentario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar comentario
            $id = $_GET['id'];
            $sql = "SELECT * FROM comentarios_productos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $comentario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $producto_id = $_POST['producto_id'];
                $cliente_id = $_POST['cliente_id'];
                $comentario_text = $_POST['comentario'];
                $valoracion = $_POST['valoracion'];

                if (empty($producto_id) || empty($cliente_id) || empty($comentario_text) || empty($valoracion)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE comentarios_productos SET producto_id = ?, cliente_id = ?, comentario = ?, valoracion = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$producto_id, $cliente_id, $comentario_text, $valoracion, $id])) {
                            echo '<div class="alert alert-success">Comentario actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el comentario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Comentario</h2>
            <form method="POST" action="crud_comentarios_productos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">ID del Producto</label>
                    <input type="number" name="producto_id" class="form-control" value="<?php echo $comentario['producto_id']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">ID del Cliente</label>
                    <input type="number" name="cliente_id" class="form-control" value="<?php echo $comentario['cliente_id']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario</label>
                    <textarea name="comentario" class="form-control" required><?php echo $comentario['comentario']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="valoracion" class="form-label">Valoración (1-5)</label>
                    <input type="number" name="valoracion" class="form-control" min="1" max="5" value="<?php echo $comentario['valoracion']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Comentario</button>
            </form>
            <br>
            <a href="crud_comentarios_productos.php" class="btn btn-secondary">Ver comentarios</a>
            <?php
            break;

        case 'delete':
            // Eliminar comentario
            $id = $_GET['id'];
            $sql = "DELETE FROM comentarios_productos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Comentario eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el comentario.</div>';
            }
            echo '<a href="crud_comentarios_productos.php" class="btn btn-secondary">Volver a la lista</a>';
            break;

        default:
            // Acción no válida
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
