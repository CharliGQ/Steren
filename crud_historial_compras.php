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
    <title>CRUD Historial Compras</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear historial de compra
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cliente_id = $_POST['cliente_id'];
                $pedido_id = $_POST['pedido_id'];
                $total_pagado = $_POST['total_pagado'];

                // Validación básica
                if (empty($cliente_id) || empty($pedido_id) || empty($total_pagado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO historial_compras (cliente_id, pedido_id, total_pagado) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$cliente_id, $pedido_id, $total_pagado])) {
                            echo '<div class="alert alert-success">Compra registrada exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al registrar la compra.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Historial de Compra</h2>
            <form method="POST" action="crud_historial_compras.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php
                        // Obtener clientes para el select
                        $sql = "SELECT id, nombre FROM clientes";
                        $stmt = $pdo->query($sql);
                        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($clientes as $cliente) {
                            echo "<option value=\"{$cliente['id']}\">{$cliente['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">Pedido</label>
                    <select name="pedido_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php
                        // Obtener pedidos para el select
                        $sql = "SELECT id FROM pedidos";
                        $stmt = $pdo->query($sql);
                        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($pedidos as $pedido) {
                            echo "<option value=\"{$pedido['id']}\">Pedido ID: {$pedido['id']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="total_pagado" class="form-label">Total Pagado</label>
                    <input type="number" step="0.01" name="total_pagado" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Compra</button>
            </form>
            <br>
            <a href="crud_historial_compras.php" class="btn btn-secondary">Ver historial de compras</a>
            <?php
            break;

        case 'read':
            // Leer historial de compras
            $sql = "SELECT hc.id, c.nombre AS cliente_nombre, p.id AS pedido_id, hc.fecha_compra, hc.total_pagado 
                    FROM historial_compras hc 
                    JOIN clientes c ON hc.cliente_id = c.id 
                    JOIN pedidos p ON hc.pedido_id = p.id";
            $stmt = $pdo->query($sql);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Historial de Compras</h2>
            <a href="crud_historial_compras.php?action=create" class="btn btn-success mb-3">Agregar Compra</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Pedido ID</th>
                    <th>Fecha de Compra</th>
                    <th>Total Pagado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($historial as $registro) { ?>
                    <tr>
                        <td><?php echo $registro['id']; ?></td>
                        <td><?php echo $registro['cliente_nombre']; ?></td>
                        <td><?php echo $registro['pedido_id']; ?></td>
                        <td><?php echo $registro['fecha_compra']; ?></td>
                        <td><?php echo $registro['total_pagado']; ?></td>
                        <td>
                            <a href="crud_historial_compras.php?action=update&id=<?php echo $registro['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_historial_compras.php?action=delete&id=<?php echo $registro['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar historial de compra
            $id = $_GET['id'];
            $sql = "SELECT * FROM historial_compras WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cliente_id = $_POST['cliente_id'];
                $pedido_id = $_POST['pedido_id'];
                $total_pagado = $_POST['total_pagado'];

                if (empty($cliente_id) || empty($pedido_id) || empty($total_pagado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE historial_compras SET cliente_id = ?, pedido_id = ?, total_pagado = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$cliente_id, $pedido_id, $total_pagado, $id])) {
                            echo '<div class="alert alert-success">Compra actualizada exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar la compra.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Historial de Compra</h2>
            <form method="POST" action="crud_historial_compras.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <?php
                        // Obtener clientes para el select
                        $sql = "SELECT id, nombre FROM clientes";
                        $stmt = $pdo->query($sql);
                        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($clientes as $cliente) {
                            $selected = $cliente['id'] == $registro['cliente_id'] ? 'selected' : '';
                            echo "<option value=\"{$cliente['id']}\" $selected>{$cliente['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">Pedido</label>
                    <select name="pedido_id" class="form-select" required>
                        <?php
                        // Obtener pedidos para el select
                        $sql = "SELECT id FROM pedidos";
                        $stmt = $pdo->query($sql);
                        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($pedidos as $pedido) {
                            $selected = $pedido['id'] == $registro['pedido_id'] ? 'selected' : '';
                            echo "<option value=\"{$pedido['id']}\" $selected>Pedido ID: {$pedido['id']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="total_pagado" class="form-label">Total Pagado</label>
                    <input type="number" step="0.01" name="total_pagado" class="form-control" value="<?php echo $registro['total_pagado']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Compra</button>
            </form>
            <br>
            <a href="crud_historial_compras.php" class="btn btn-secondary">Volver a la lista</a>
            <?php
            break;

        case 'delete':
            // Eliminar historial de compra
            $id = $_GET['id'];
            $sql = "DELETE FROM historial_compras WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Compra eliminada exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar la compra.</div>';
            }
            echo '<a href="crud_historial_compras.php" class="btn btn-secondary">Volver a la lista</a>';
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
