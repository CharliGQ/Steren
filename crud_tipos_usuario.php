<?php
// Incluir la conexión a la base de datos desde el archivo db.php
include 'index.php';
require_once 'db.php';

// Obtener la acción (crear, leer, actualizar, eliminar) desde la URL, por defecto 'read' para leer
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Tipos de Usuario</title>
    <!-- Incluir el CSS de Bootstrap para darle estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejar las diferentes acciones (crear, leer, actualizar, eliminar)
    switch ($action) {
        case 'create':
            // Acción para crear un nuevo tipo de usuario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener el nombre del tipo de usuario desde el formulario
                $tipo_nombre = $_POST['tipo_nombre'];

                // Validar que el campo no esté vacío
                if (empty($tipo_nombre)) {
                    // Mostrar un mensaje de error si el campo está vacío
                    echo '<div class="alert alert-danger">El campo tipo de usuario es obligatorio.</div>';
                } else {
                    // Intentar insertar el nuevo tipo de usuario en la base de datos
                    try {
                        $sql = "INSERT INTO tipos_usuario (tipo_nombre) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        // Ejecutar la consulta con el nombre del tipo de usuario
                        if ($stmt->execute([$tipo_nombre])) {
                            // Mostrar mensaje de éxito si se inserta correctamente
                            echo '<div class="alert alert-success">Tipo de usuario creado exitosamente.</div>';
                        } else {
                            // Mostrar mensaje de error si ocurre un problema en la inserción
                            echo '<div class="alert alert-danger">Error al crear el tipo de usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        // Capturar y mostrar el error en caso de excepción
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Tipo de Usuario</h2>
            <!-- Formulario para crear un nuevo tipo de usuario -->
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
            // Acción para leer (listar) los tipos de usuario existentes
            // Consultar todos los registros de la tabla tipos_usuario
            $sql = "SELECT * FROM tipos_usuario";
            $stmt = $pdo->query($sql);
            // Obtener los resultados como un arreglo asociativo
            $tipos_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Tipos de Usuario</h2>
            <!-- Botón para crear un nuevo tipo de usuario -->
            <a href="crud_tipos_usuario.php?action=create" class="btn btn-success mb-3">Crear Tipo de Usuario</a>
            <!-- Tabla que muestra los tipos de usuario existentes -->
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ciclo para mostrar cada tipo de usuario en una fila de la tabla -->
                <?php foreach ($tipos_usuario as $tipo) { ?>
                    <tr>
                        <td><?php echo $tipo['id']; ?></td>
                        <td><?php echo $tipo['tipo_nombre']; ?></td>
                        <td>
                            <!-- Botón para editar el tipo de usuario, redirige a la acción 'update' -->
                            <a href="crud_tipos_usuario.php?action=update&id=<?php echo $tipo['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <!-- Botón para eliminar el tipo de usuario, con confirmación previa -->
                            <a href="crud_tipos_usuario.php?action=delete&id=<?php echo $tipo['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Acción para actualizar (editar) un tipo de usuario
            // Obtener el ID del tipo de usuario desde la URL
            $id = $_GET['id'];
            // Consultar el tipo de usuario correspondiente al ID
            $sql = "SELECT * FROM tipos_usuario WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si se envía el formulario (POST), actualizar el tipo de usuario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $tipo_nombre = $_POST['tipo_nombre'];

                // Validar que el campo no esté vacío
                if (empty($tipo_nombre)) {
                    // Mostrar mensaje de error si el campo está vacío
                    echo '<div class="alert alert-danger">El campo tipo de usuario es obligatorio.</div>';
                } else {
                    // Intentar actualizar el registro en la base de datos
                    try {
                        $sql = "UPDATE tipos_usuario SET tipo_nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        // Ejecutar la actualización con el nuevo nombre y el ID
                        if ($stmt->execute([$tipo_nombre, $id])) {
                            // Mostrar mensaje de éxito si la actualización fue exitosa
                            echo '<div class="alert alert-success">Tipo de usuario actualizado exitosamente.</div>';
                        } else {
                            // Mostrar mensaje de error si algo falla
                            echo '<div class="alert alert-danger">Error al actualizar el tipo de usuario.</div>';
                        }
                    } catch (PDOException $e) {
                        // Capturar y mostrar el error en caso de excepción
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Tipo de Usuario</h2>
            <!-- Formulario prellenado con el nombre del tipo de usuario para su edición -->
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
            // Acción para eliminar un tipo de usuario
            // Obtener el ID del tipo de usuario a eliminar desde la URL
            $id = $_GET['id'];
            try {
                // Ejecutar la consulta para eliminar el registro correspondiente
                $sql = "DELETE FROM tipos_usuario WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                // Si la eliminación es exitosa, redirigir a la acción 'read'
                if ($stmt->execute([$id])) {
                    header("Location: crud_tipos_usuario.php?action=read");
                    exit;
                } else {
                    // Mostrar mensaje de error si ocurre algún problema
                    echo '<div class="alert alert-danger">Error al eliminar el tipo de usuario.</div>';
                }
            } catch (PDOException $e) {
                // Capturar y mostrar el error en caso de excepción
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            break;
    }
    ?>
</div>

<!-- Incluir el JS de Bootstrap para funcionalidades interactivas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
