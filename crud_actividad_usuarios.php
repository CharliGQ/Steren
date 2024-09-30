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
    <title>CRUD Actividad de Usuarios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear actividad de usuario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $usuario_id = $_POST['usuario_id'];
                $accion = $_POST['accion'];
                $ip = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario

                // Validación básica
                if (empty($usuario_id) || empty($accion)) {
                    echo '<div class="alert alert-danger">El usuario y la acción son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO actividad_usuarios (usuario_id, accion, ip) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$usuario_id, $accion, $ip])) {
                            echo '<div class="alert alert-success">Actividad registrada exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al registrar la actividad.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Registrar Actividad de Usuario</h2>
            <form method="POST" action="crud_actividad_usuarios.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="usuario_id" class="form-label">Usuario</label>
                    <select name="usuario_id" class="form-select" required>
                        <?php
                        // Obtener usuarios para el dropdown
                        $usuarios = $pdo->query("SELECT id, correo FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($usuarios as $usuario) {
                            echo '<option value="' . $usuario['id'] . '">' . $usuario['correo'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="accion" class="form-label">Acción</label>
                    <input type="text" name="accion" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Actividad</button>
            </form>
            <br>
            <a href="crud_actividad_usuarios.php" class="btn btn-secondary">Ver Actividades</a>
            <?php
            break;

        case 'read':
            // Leer actividades de usuarios
            $sql = "SELECT au.*, u.correo AS usuario_correo 
                    FROM actividad_usuarios au 
                    JOIN usuarios u ON au.usuario_id = u.id";
            $stmt = $pdo->query($sql);
            $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Actividades de Usuarios</h2>
            <a href="crud_actividad_usuarios.php?action=create" class="btn btn-success mb-3">Registrar Nueva Actividad</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Fecha Actividad</th>
                    <th>IP</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($actividades as $actividad) { ?>
                    <tr>
                        <td><?php echo $actividad['id']; ?></td>
                        <td><?php echo $actividad['usuario_correo']; ?></td>
                        <td><?php echo $actividad['accion']; ?></td>
                        <td><?php echo $actividad['fecha_actividad']; ?></td>
                        <td><?php echo $actividad['ip']; ?></td>
                        <td>
                            <a href="crud_actividad_usuarios.php?action=delete&id=<?php echo $actividad['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'delete':
            // Eliminar actividad de usuario
            $id = $_GET['id'];
            $sql = "DELETE FROM actividad_usuarios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Actividad eliminada exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar la actividad.</div>';
            }
            echo '<a href="crud_actividad_usuarios.php" class="btn btn-secondary">Volver a la lista</a>';
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
