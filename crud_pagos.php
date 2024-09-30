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
    <title>CRUD Pagos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear pago
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pedido_id = $_POST['pedido_id'];
                $metodo_pago_id = $_POST['metodo_pago_id'];
                $monto_pagado = $_POST['monto_pagado'];
                $estado = $_POST['estado'];

                // Validación básica
                if (empty($pedido_id) || empty($metodo_pago_id) || empty($monto_pagado) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO pagos (pedido_id, metodo_pago_id, monto_pagado, estado) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$pedido_id, $metodo_pago_id, $monto_pagado, $estado])) {
                            echo '<div class="alert alert-success">Pago agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el pago.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Pago</h2>
            <form method="POST" action="crud_pagos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="metodo_pago_id" class="form-label">ID del Método de Pago</label>
                    <input type="number" name="metodo_pago_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="monto_pagado" class="form-label">Monto Pagado</label>
                    <input type="number" step="0.01" name="monto_pagado" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Pago</button>
            </form>
            <br>
            <a href="crud_pagos.php" class="btn btn-secondary">Ver pagos</a>
            <?php
            break;

        case 'read':
            // Leer pagos
            $sql = "SELECT p.*, mp.nombre_metodo FROM pagos p JOIN metodos_pago mp ON p.metodo_pago_id = mp.id";
            $stmt = $pdo->query($sql);
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Pagos</h2>
            <a href="crud_pagos.php?action=create" class="btn btn-success mb-3">Agregar Pago</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID del Pedido</th>
                    <th>Método de Pago</th>
                    <th>Monto Pagado</th>
                    <th>Fecha de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagos as $pago) { ?>
                    <tr>
                        <td><?php echo $pago['id']; ?></td>
                        <td><?php echo $pago['pedido_id']; ?></td>
                        <td><?php echo $pago['nombre_metodo']; ?></td>
                        <td><?php echo $pago['monto_pagado']; ?></td>
                        <td><?php echo $pago['fecha_pago']; ?></td>
                        <td><?php echo $pago['estado']; ?></td>
                        <td>
                            <a href="crud_pagos.php?action=update&id=<?php echo $pago['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_pagos.php?action=delete&id=<?php echo $pago['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar pago
            $id = $_GET['id'];
            $sql = "SELECT * FROM pagos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pedido_id = $_POST['pedido_id'];
                $metodo_pago_id = $_POST['metodo_pago_id'];
                $monto_pagado = $_POST['monto_pagado'];
                $estado = $_POST['estado'];

                if (empty($pedido_id) || empty($metodo_pago_id) || empty($monto_pagado) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE pagos SET pedido_id = ?, metodo_pago_id = ?, monto_pagado = ?, estado = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$pedido_id, $metodo_pago_id, $monto_pagado, $estado, $id])) {
                            echo '<div class="alert alert-success">Pago actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el pago.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Pago</h2>
            <form method="POST" action="crud_pagos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" value="<?php echo $pago['pedido_id']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="metodo_pago_id" class="form-label">ID del Método de Pago</label>
                    <input type="number" name="metodo_pago_id" class="form-control" value="<?php echo $pago['metodo_pago_id']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="monto_pagado" class="form-label">Monto Pagado</label>
                    <input type="number" step="0.01" name="monto_pagado" class="form-control" value="<?php echo $pago['monto_pagado']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" value="<?php echo $pago['estado']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Pago</button>
            </form>
            <br>
            <a href="crud_pagos.php" class="btn btn-secondary">Ver pagos</a>
            <?php
            break;

        case 'delete':
            // Eliminar pago
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM pagos WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_pagos.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el pago.</div>';
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
