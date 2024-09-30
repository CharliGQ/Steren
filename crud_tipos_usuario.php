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
    <title>CRUD Tipos de Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear tipo de usuario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $tipo_nombre = $_POST['tipo_nombre'];

                // Validación básica
                if (empty($tipo_nombre)) {
                    echo '<div class="alert alert-danger">El campo tipo de usuario es obligatorio.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO tipos_usuario (tipo_nombre) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$tipo_nombre])) {
                            echo '<div class="alert alert-success">Tipo de usuario creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el tipo de usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Tipo de Usuario</h2>
            <form method="POST" action="crud_tipos_usuario.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="tipo_nombre" class="form-label">Tipo de Usuario</label>
                    <input type="text" name="tipo_nombre" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Tipo de Usuario</button>
            </form>
            <br>
            <a href="crud_tipos_usuario.php" class="btn btn-secondary">Ver tipos de usuario</a>
            <?php
            break;

        case 'read':
            // Leer tipos de usuario
            $sql = "SELECT * FROM tipos_usuario";
            $stmt = $pdo->query($sql);
            $tipos_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Tipos de Usuario</h2>
            <a href="crud_tipos_usuario.php?action=create" class="btn btn-success mb-3">Crear Tipo de Usuario</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tipos_usuario as $tipo) { ?>
                    <tr>
                        <td><?php echo $tipo['id']; ?></td>
                        <td><?php echo $tipo['tipo_nombre']; ?></td>
                        <td>
                            <a href="crud_tipos_usuario.php?action=update&id=<?php echo $tipo['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_tipos_usuario.php?action=delete&id=<?php echo $tipo['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar tipo de usuario
            $id = $_GET['id'];
            $sql = "SELECT * FROM tipos_usuario WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $tipo_nombre = $_POST['tipo_nombre'];

                if (empty($tipo_nombre)) {
                    echo '<div class="alert alert-danger">El campo tipo de usuario es obligatorio.</div>';
                } else {
                    try {
                        $sql = "UPDATE tipos_usuario SET tipo_nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$tipo_nombre, $id])) {
                            echo '<div class="alert alert-success">Tipo de usuario actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el tipo de usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Tipo de Usuario</h2>
            <form method="POST" action="crud_tipos_usuario.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="tipo_nombre" class="form-label">Tipo de Usuario</label>
                    <input type="text" name="tipo_nombre" value="<?php echo $tipo['tipo_nombre']; ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Tipo de Usuario</button>
            </form>
            <br>
            <a href="crud_tipos_usuario.php" class="btn btn-secondary">Ver tipos de usuario</a>
            <?php
            break;

        case 'delete':
            // Eliminar tipo de usuario
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM tipos_usuario WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_tipos_usuario.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el tipo de usuario.</div>';
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
