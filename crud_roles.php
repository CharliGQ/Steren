<?php
// Incluir la conexión a la base de datos desde el archivo db.php
include 'index.php';
require_once 'db.php';

// Obtener la acción de la URL (crear, leer, actualizar, eliminar), si no se especifica, por defecto es 'read'
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Incluir la cabecera HTML y la CDN de Bootstrap para estilos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Roles</title>
    <!-- Incluir el CSS de Bootstrap desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Estructura de control que maneja las diferentes acciones
    switch ($action) {
        case 'create':
            // Acción para crear un nuevo rol
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener el nombre del rol desde el formulario
                $nombre = $_POST['nombre'];

                // Validación: si el campo está vacío, mostrar un mensaje de error
                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre es obligatorio.</div>';
                } else {
                    // Intentar insertar el nuevo rol en la base de datos
                    try {
                        $sql = "INSERT INTO roles (nombre) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        // Si se ejecuta correctamente, mostrar un mensaje de éxito
                        if ($stmt->execute([$nombre])) {
                            echo '<div class="alert alert-success">Rol creado exitosamente.</div>';
                        } else {
                            // Si algo falla, mostrar un mensaje de error
                            echo '<div class="alert alert-danger">Error al crear el rol.</div>';
                        }
                    } catch (PDOException $e) {
                        // Mostrar mensaje de error si hay una excepción (error en la base de datos)
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Rol</h2>
            <!-- Formulario para agregar un nuevo rol -->
            <form method="POST" action="crud_roles.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Rol</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Rol</button>
            </form>
            <br>
            <!-- Enlace para volver a la lista de roles -->
            <a href="crud_roles.php" class="btn btn-secondary">Ver Roles</a>
            <?php
            break;

        case 'read':
            // Acción para leer y mostrar la lista de roles
            $sql = "SELECT * FROM roles";  // Consulta SQL para obtener todos los roles
            $stmt = $pdo->query($sql);     // Ejecutar la consulta
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Obtener los resultados en un arreglo asociativo
            ?>
            <h2 class="mb-4">Lista de Roles</h2>
            <!-- Botón para agregar un nuevo rol -->
            <a href="crud_roles.php?action=create" class="btn btn-success mb-3">Agregar Rol</a>
            <!-- Tabla para mostrar los roles existentes -->
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ciclo para recorrer cada rol y mostrarlo en la tabla -->
                <?php foreach ($roles as $rol) { ?>
                    <tr>
                        <td><?php echo $rol['id']; ?></td>
                        <td><?php echo $rol['nombre']; ?></td>
                        <td>
                            <!-- Botón para editar el rol, redirige a la acción 'update' -->
                            <a href="crud_roles.php?action=update&id=<?php echo $rol['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <!-- Botón para eliminar el rol, solicita confirmación antes de proceder -->
                            <a href="crud_roles.php?action=delete&id=<?php echo $rol['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Acción para actualizar (editar) un rol existente
            $id = $_GET['id'];  // Obtener el ID del rol desde la URL
            // Consulta para obtener los datos del rol a editar
            $sql = "SELECT * FROM roles WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);  // Ejecutar la consulta con el ID
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);  // Obtener los datos del rol

            // Si el formulario fue enviado (POST), actualizar el rol
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];  // Obtener el nuevo nombre del rol desde el formulario

                // Validar que el campo nombre no esté vacío
                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre es obligatorio.</div>';
                } else {
                    // Intentar actualizar el registro en la base de datos
                    try {
                        $sql = "UPDATE roles SET nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        // Ejecutar la actualización con los datos nuevos
                        if ($stmt->execute([$nombre, $id])) {
                            echo '<div class="alert alert-success">Rol actualizado exitosamente.</div>';
                        } else {
                            // Mostrar mensaje de error si ocurre un problema
                            echo '<div class="alert alert-danger">Error al actualizar el rol.</div>';
                        }
                    } catch (PDOException $e) {
                        // Capturar y mostrar el error en caso de una excepción
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Rol</h2>
            <!-- Formulario prellenado con los datos del rol para su edición -->
            <form method="POST" action="crud_roles.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Rol</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo $rol['nombre']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Rol</button>
            </form>
            <br>
            <!-- Enlace para volver a la lista de roles -->
            <a href="crud_roles.php" class="btn btn-secondary">Volver a la lista</a>
            <?php
            break;

        case 'delete':
            // Acción para eliminar un rol existente
            $id = $_GET['id'];  // Obtener el ID del rol desde la URL
            $sql = "DELETE FROM roles WHERE id = ?";  // Consulta para eliminar el rol por ID
            $stmt = $pdo->prepare($sql);
            // Ejecutar la consulta, si es exitosa mostrar un mensaje de confirmación
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Rol eliminado exitosamente.</div>';
            } else {
                // Si falla, mostrar un mensaje de error
                echo '<div class="alert alert-danger">Error al eliminar el rol.</div>';
            }
            // Enlace para volver a la lista de roles
            echo '<a href="crud_roles.php" class="btn btn-secondary">Volver a la lista</a>';
            break;

        default:
            // Si la acción no es válida, mostrar un mensaje de error
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Incluir el JavaScript de Bootstrap (opcional) para funcionalidades interactivas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
