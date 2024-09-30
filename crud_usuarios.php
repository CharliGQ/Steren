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
    <title>CRUD Usuarios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear usuario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $correo = $_POST['correo'];
                $contrasena = $_POST['contrasena'];
                $rol_id = $_POST['rol_id'];
                $nombre = $_POST['nombre'];

                // Validación básica
                if (empty($correo) || empty($contrasena)) {
                    echo '<div class="alert alert-danger">El correo y la contraseña son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO usuarios (correo, contrasena, rol_id, nombre) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$correo, password_hash($contrasena, PASSWORD_DEFAULT), $rol_id, $nombre])) {
                            echo '<div class="alert alert-success">Usuario creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Usuario</h2>
            <form method="POST" action="crud_usuarios.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select name="rol_id" class="form-select" required>
                        <?php
                        // Obtener roles para el dropdown
                        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roles as $rol) {
                            echo '<option value="' . $rol['id'] . '">' . $rol['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Agregar Usuario</button>
            </form>
            <br>
            <a href="crud_usuarios.php" class="btn btn-secondary">Ver Usuarios</a>
            <?php
            break;

        case 'read':
            // Leer usuarios
            $sql = "SELECT u.*, r.nombre AS rol_nombre FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id";
            $stmt = $pdo->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Usuarios</h2>
            <a href="crud_usuarios.php?action=create" class="btn btn-success mb-3">Agregar Usuario</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['correo']; ?></td>
                        <td><?php echo $usuario['nombre']; ?></td>
                        <td><?php echo $usuario['rol_nombre']; ?></td>
                        <td><?php echo $usuario['fecha_registro']; ?></td>
                        <td>
                            <a href="crud_usuarios.php?action=update&id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar usuario
            $id = $_GET['id'];
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $correo = $_POST['correo'];
                $rol_id = $_POST['rol_id'];
                $nombre = $_POST['nombre'];

                if (empty($correo)) {
                    echo '<div class="alert alert-danger">El correo es obligatorio.</div>';
                } else {
                    try {
                        $sql = "UPDATE usuarios SET correo = ?, rol_id = ?, nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$correo, $rol_id, $nombre, $id])) {
                            echo '<div class="alert alert-success">Usuario actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Usuario</h2>
            <form method="POST" action="crud_usuarios.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="<?php echo $usuario['correo']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select name="rol_id" class="form-select" required>
                        <?php
                        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roles as $rol) {
                            $selected = ($rol['id'] == $usuario['rol_id']) ? 'selected' : '';
                            echo '<option value="' . $rol['id'] . '" ' . $selected . '>' . $rol['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
            <br>
            <a href="crud_usuarios.php" class="btn btn-secondary">Volver a la lista</a>
            <?php
            break;

        case 'delete':
            // Eliminar usuario
            $id = $_GET['id'];
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Usuario eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el usuario.</div>';
            }
            echo '<a href="crud_usuarios.php" class="btn btn-secondary">Volver a la lista</a>';
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
