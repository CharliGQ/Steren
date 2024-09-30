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
    <title>CRUD Envíos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear envío
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pedido_id = $_POST['pedido_id'];
                $direccion_envio = $_POST['direccion_envio'];
                $estado = $_POST['estado'];

                // Validación básica
                if (empty($pedido_id) || empty($direccion_envio) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO envios (pedido_id, direccion_envio, estado) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$pedido_id, $direccion_envio, $estado])) {
                            echo '<div class="alert alert-success">Envío agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el envío.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Envío</h2>
            <form method="POST" action="crud_envios.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="direccion_envio" class="form-label">Dirección de Envío</label>
                    <input type="text" name="direccion_envio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Envío</button>
            </form>
            <br>
            <a href="crud_envios.php" class="btn btn-secondary">Ver envíos</a>
            <?php
            break;

        case 'read':
            // Leer envíos
            $sql = "SELECT e.*, p.nombre_cliente FROM envios e JOIN pedidos p ON e.pedido_id = p.id";
            $stmt = $pdo->query($sql);
            $envios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Envíos</h2>
            <a href="crud_envios.php?action=create" class="btn btn-success mb-3">Agregar Envío</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID del Pedido</th>
                    <th>Nombre del Cliente</th>
                    <th>Dirección de Envío</th>
                    <th>Fecha de Envío</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($envios as $envio) { ?>
                    <tr>
                        <td><?php echo $envio['id']; ?></td>
                        <td><?php echo $envio['pedido_id']; ?></td>
                        <td><?php echo $envio['nombre_cliente']; ?></td>
                        <td><?php echo $envio['direccion_envio']; ?></td>
                        <td><?php echo $envio['fecha_envio']; ?></td>
                        <td><?php echo $envio['estado']; ?></td>
                        <td>
                            <a href="crud_envios.php?action=update&id=<?php echo $envio['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_envios.php?action=delete&id=<?php echo $envio['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar envío
            $id = $_GET['id'];
            $sql = "SELECT * FROM envios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $envio = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pedido_id = $_POST['pedido_id'];
                $direccion_envio = $_POST['direccion_envio'];
                $estado = $_POST['estado'];

                if (empty($pedido_id) || empty($direccion_envio) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE envios SET pedido_id = ?, direccion_envio = ?, estado = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$pedido_id, $direccion_envio, $estado, $id])) {
                            echo '<div class="alert alert-success">Envío actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el envío.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Envío</h2>
            <form method="POST" action="crud_envios.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" value="<?php echo $envio['pedido_id']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="direccion_envio" class="form-label">Dirección de Envío</label>
                    <input type="text" name="direccion_envio" class="form-control" value="<?php echo $envio['direccion_envio']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" value="<?php echo $envio['estado']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Envío</button>
            </form>
            <br>
            <a href="crud_envios.php" class="btn btn-secondary">Ver envíos</a>
            <?php
            break;

        case 'delete':
            // Eliminar envío
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM envios WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_envios.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el envío.</div>';
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
