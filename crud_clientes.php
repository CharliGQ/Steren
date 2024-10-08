<?php
// Incluir la conexión a la base de datos
include 'index.php';
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
    <title>CRUD Clientes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear cliente
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $telefono = $_POST['telefono'];
                $direccion = $_POST['direccion'];

                // Validación básica
                if (empty($nombre) || empty($correo)) {
                    echo '<div class="alert alert-danger">Nombre y correo son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO clientes (nombre, correo, telefono, direccion) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $correo, $telefono, $direccion])) {
                            echo '<div class="alert alert-success">Cliente creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el cliente.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Cliente</h2>
            <form method="POST" action="crud_clientes.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Crear Cliente</button>
            </form>
            <br>
            <a href="crud_clientes.php" class="btn btn-secondary">Ver clientes</a>
            <?php
            break;

        case 'read':
            // Leer clientes
            $sql = "SELECT * FROM clientes";
            $stmt = $pdo->query($sql);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Clientes</h2>
            <a href="crud_clientes.php?action=create" class="btn btn-success mb-3">Crear Cliente</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clientes as $cliente) { ?>
                    <tr>
                        <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $cliente['correo']; ?></td>
                        <td><?php echo $cliente['telefono']; ?></td>
                        <td><?php echo $cliente['direccion']; ?></td>
                        <td>
                            <a href="crud_clientes.php?action=update&id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_clientes.php?action=delete&id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar cliente
            $id = $_GET['id'];
            $sql = "SELECT * FROM clientes WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $telefono = $_POST['telefono'];
                $direccion = $_POST['direccion'];

                if (empty($nombre) || empty($correo)) {
                    echo '<div class="alert alert-danger">Nombre y correo son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE clientes SET nombre = ?, correo = ?, telefono = ?, direccion = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $correo, $telefono, $direccion, $id])) {
                            echo '<div class="alert alert-success">Cliente actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el cliente.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Cliente</h2>
            <form method="POST" action="crud_clientes.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $cliente['nombre']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" value="<?php echo $cliente['correo']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo $cliente['telefono']; ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="<?php echo $cliente['direccion']; ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
            </form>
            <br>
            <a href="crud_clientes.php" class="btn btn-secondary">Ver clientes</a>
            <?php
            break;

        case 'delete':
            // Eliminar cliente
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM clientes WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_clientes.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el cliente.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            break;
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
