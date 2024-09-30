<?php
// Incluir la conexión a la base de datos
require_once 'db.php';

// Definir la acción (crear, leer, actualizar, eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Obtener tipos de usuario para el dropdown
$sql = "SELECT * FROM tipos_usuario";
$stmt = $pdo->query($sql);
$tipos_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Empleados</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear empleado
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $telefono = $_POST['telefono'];
                $tipo_usuario_id = $_POST['tipo_usuario_id'];

                // Validación básica
                if (empty($nombre) || empty($correo) || empty($tipo_usuario_id)) {
                    echo '<div class="alert alert-danger">Los campos nombre, correo y tipo de usuario son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO empleados (nombre, correo, telefono, tipo_usuario_id) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $correo, $telefono, $tipo_usuario_id])) {
                            echo '<div class="alert alert-success">Empleado creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el empleado.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Empleado</h2>
            <form method="POST" action="crud_empleados.php?action=create" class="bg-white p-4 shadow-sm rounded">
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
                    <label for="tipo_usuario_id" class="form-label">Tipo de Usuario</label>
                    <select name="tipo_usuario_id" class="form-select" required>
                        <option value="">Seleccione un tipo de usuario</option>
                        <?php foreach ($tipos_usuario as $tipo) { ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['tipo_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear Empleado</button>
            </form>
            <br>
            <a href="crud_empleados.php" class="btn btn-secondary">Ver empleados</a>
            <?php
            break;

        case 'read':
            // Leer empleados
            $sql = "SELECT e.*, tu.tipo_nombre FROM empleados e LEFT JOIN tipos_usuario tu ON e.tipo_usuario_id = tu.id";
            $stmt = $pdo->query($sql);
            $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Empleados</h2>
            <a href="crud_empleados.php?action=create" class="btn btn-success mb-3">Crear Empleado</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($empleados as $empleado) { ?>
                    <tr>
                        <td><?php echo $empleado['id']; ?></td>
                        <td><?php echo $empleado['nombre']; ?></td>
                        <td><?php echo $empleado['correo']; ?></td>
                        <td><?php echo $empleado['telefono']; ?></td>
                        <td><?php echo $empleado['tipo_nombre']; ?></td>
                        <td>
                            <a href="crud_empleados.php?action=update&id=<?php echo $empleado['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_empleados.php?action=delete&id=<?php echo $empleado['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar empleado
            $id = $_GET['id'];
            $sql = "SELECT * FROM empleados WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener tipos de usuario para el dropdown
            $sql = "SELECT * FROM tipos_usuario";
            $stmt = $pdo->query($sql);
            $tipos_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $telefono = $_POST['telefono'];
                $tipo_usuario_id = $_POST['tipo_usuario_id'];

                if (empty($nombre) || empty($correo) || empty($tipo_usuario_id)) {
                    echo '<div class="alert alert-danger">Los campos nombre, correo y tipo de usuario son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE empleados SET nombre = ?, correo = ?, telefono = ?, tipo_usuario_id = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $correo, $telefono, $tipo_usuario_id, $id])) {
                            echo '<div class="alert alert-success">Empleado actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el empleado.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Empleado</h2>
            <form method="POST" action="crud_empleados.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $empleado['nombre']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" value="<?php echo $empleado['correo']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo $empleado['telefono']; ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="tipo_usuario_id" class="form-label">Tipo de Usuario</label>
                    <select name="tipo_usuario_id" class="form-select" required>
                        <option value="">Seleccione un tipo de usuario</option>
                        <?php foreach ($tipos_usuario as $tipo) { ?>
                            <option value="<?php echo $tipo['id']; ?>" <?php if ($tipo['id'] == $empleado['tipo_usuario_id']) echo 'selected'; ?>>
                                <?php echo $tipo['tipo_nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Empleado</button>
            </form>
            <br>
            <a href="crud_empleados.php" class="btn btn-secondary">Ver empleados</a>
            <?php
            break;

        case 'delete':
            // Eliminar empleado
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM empleados WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_empleados.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el empleado.</div>';
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
