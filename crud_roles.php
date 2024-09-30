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
    <title>CRUD Roles</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear rol
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];

                // Validación básica
                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre es obligatorio.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO roles (nombre) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre])) {
                            echo '<div class="alert alert-success">Rol creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el rol.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Rol</h2>
            <form method="POST" action="crud_roles.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Rol</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Rol</button>
            </form>
            <br>
            <a href="crud_roles.php" class="btn btn-secondary">Ver Roles</a>
            <?php
            break;

        case 'read':
            // Leer roles
            $sql = "SELECT * FROM roles";
            $stmt = $pdo->query($sql);
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Roles</h2>
            <a href="crud_roles.php?action=create" class="btn btn-success mb-3">Agregar Rol</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roles as $rol) { ?>
                    <tr>
                        <td><?php echo $rol['id']; ?></td>
                        <td><?php echo $rol['nombre']; ?></td>
                        <td>
                            <a href="crud_roles.php?action=update&id=<?php echo $rol['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_roles.php?action=delete&id=<?php echo $rol['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar rol
            $id = $_GET['id'];
            $sql = "SELECT * FROM roles WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];

                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre es obligatorio.</div>';
                } else {
                    try {
                        $sql = "UPDATE roles SET nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $id])) {
                            echo '<div class="alert alert-success">Rol actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el rol.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Rol</h2>
            <form method="POST" action="crud_roles.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Rol</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo $rol['nombre']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Rol</button>
            </form>
            <br>
            <a href="crud_roles.php" class="btn btn-secondary">Volver a la lista</a>
            <?php
            break;

        case 'delete':
            // Eliminar rol
            $id = $_GET['id'];
            $sql = "DELETE FROM roles WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Rol eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el rol.</div>';
            }
            echo '<a href="crud_roles.php" class="btn btn-secondary">Volver a la lista</a>';
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
