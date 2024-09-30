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
    <title>CRUD Administradores</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear administrador
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $usuario = $_POST['usuario'];
                $contrasena = $_POST['contrasena'];
                $correo = $_POST['correo'];

                // Validación básica
                if (empty($usuario) || empty($contrasena) || empty($correo)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO administradores (usuario, contrasena, correo) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        // Hash de la contraseña
                        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                        if ($stmt->execute([$usuario, $hashed_password, $correo])) {
                            echo '<div class="alert alert-success">Administrador creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el administrador.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Administrador</h2>
            <form method="POST" action="crud_administradores.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Administrador</button>
            </form>
            <br>
            <a href="crud_administradores.php" class="btn btn-secondary">Ver administradores</a>
            <?php
            break;

        case 'read':
            // Leer administradores
            $sql = "SELECT * FROM administradores";
            $stmt = $pdo->query($sql);
            $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Administradores</h2>
            <a href="crud_administradores.php?action=create" class="btn btn-success mb-3">Crear Administrador</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($administradores as $admin) { ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo $admin['usuario']; ?></td>
                        <td><?php echo $admin['correo']; ?></td>
                        <td>
                            <a href="crud_administradores.php?action=update&id=<?php echo $admin['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_administradores.php?action=delete&id=<?php echo $admin['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar administrador
            $id = $_GET['id'];
            $sql = "SELECT * FROM administradores WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $usuario = $_POST['usuario'];
                $correo = $_POST['correo'];
                $contrasena = $_POST['contrasena'];

                if (empty($usuario) || empty($correo)) {
                    echo '<div class="alert alert-danger">Usuario y correo son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE administradores SET usuario = ?, correo = ?, contrasena = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        // Hash de la nueva contraseña si se cambia
                        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                        if ($stmt->execute([$usuario, $correo, $hashed_password, $id])) {
                            echo '<div class="alert alert-success">Administrador actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el administrador.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Administrador</h2>
            <form method="POST" action="crud_administradores.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" value="<?php echo $admin['usuario']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña (dejar en blanco si no se desea cambiar)</label>
                    <input type="password" name="contrasena" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" value="<?php echo $admin['correo']; ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Administrador</button>
            </form>
            <br>
            <a href="crud_administradores.php" class="btn btn-secondary">Ver administradores</a>
            <?php
            break;

        case 'delete':
            // Eliminar administrador
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM administradores WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_administradores.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el administrador.</div>';
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
