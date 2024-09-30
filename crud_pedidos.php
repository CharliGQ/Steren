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
    <title>CRUD Pedidos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear pedido
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_cliente = $_POST['nombre_cliente'];
                $monto_total = $_POST['monto_total'];

                // Validación básica
                if (empty($nombre_cliente) || empty($monto_total)) {
                    echo '<div class="alert alert-danger">El nombre del cliente y el monto total son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO pedidos (nombre_cliente, monto_total) VALUES (?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_cliente, $monto_total])) {
                            echo '<div class="alert alert-success">Pedido creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el pedido.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Pedido</h2>
            <form method="POST" action="crud_pedidos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="monto_total" class="form-label">Monto Total</label>
                    <input type="number" step="0.01" name="monto_total" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Pedido</button>
            </form>
            <br>
            <a href="crud_pedidos.php" class="btn btn-secondary">Ver pedidos</a>
            <?php
            break;

        case 'read':
            // Leer pedidos
            $sql = "SELECT * FROM pedidos";
            $stmt = $pdo->query($sql);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Pedidos</h2>
            <a href="crud_pedidos.php?action=create" class="btn btn-success mb-3">Crear Pedido</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha Pedido</th>
                    <th>Nombre Cliente</th>
                    <th>Monto Total</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $pedido) { ?>
                    <tr>
                        <td><?php echo $pedido['id']; ?></td>
                        <td><?php echo $pedido['fecha_pedido']; ?></td>
                        <td><?php echo $pedido['nombre_cliente']; ?></td>
                        <td><?php echo $pedido['monto_total']; ?></td>
                        <td>
                            <a href="crud_pedidos.php?action=update&id=<?php echo $pedido['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_pedidos.php?action=delete&id=<?php echo $pedido['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar pedido
            $id = $_GET['id'];
            $sql = "SELECT * FROM pedidos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_cliente = $_POST['nombre_cliente'];
                $monto_total = $_POST['monto_total'];

                if (empty($nombre_cliente) || empty($monto_total)) {
                    echo '<div class="alert alert-danger">El nombre del cliente y el monto total son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE pedidos SET nombre_cliente = ?, monto_total = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_cliente, $monto_total, $id])) {
                            echo '<div class="alert alert-success">Pedido actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el pedido.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Pedido</h2>
            <form method="POST" action="crud_pedidos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" value="<?php echo $pedido['nombre_cliente']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="monto_total" class="form-label">Monto Total</label>
                    <input type="number" step="0.01" name="monto_total" value="<?php echo $pedido['monto_total']; ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
            </form>
            <br>
            <a href="crud_pedidos.php" class="btn btn-secondary">Ver pedidos</a>
            <?php
            break;

        case 'delete':
            // Eliminar pedido
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM pedidos WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_pedidos.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el pedido.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            break;
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
