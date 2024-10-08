<?php
// Incluir la conexión a la base de datos
include 'index.php'; // Se incluye la barra de navegación.
require_once 'db.php'; // Se incluye el archivo que contiene la configuración y conexión a la base de datos.

// Definir la acción (crear, leer, actualizar, eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : 'read'; // Se obtiene la acción a realizar (CRUD). Si no se especifica, se asume 'read' como predeterminada.

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Usuarios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Se incluye el framework CSS Bootstrap para el diseño. -->
</head>
<body class="bg-light"> <!-- Se aplica una clase de fondo claro al body. -->

<div class="container mt-5"> <!-- Contenedor principal con margen superior para la separación del contenido. -->
    <?php
    // Manejo de acciones
    switch ($action) { // Se utiliza un switch para determinar qué acción (CRUD) ejecutar.
        case 'create': // Caso de crear un nuevo usuario.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Se verifica si el método es POST (envío de formulario).
                $correo = $_POST['correo']; // Captura del correo enviado en el formulario.
                $contrasena = $_POST['contrasena']; // Captura de la contraseña.
                $rol_id = $_POST['rol_id']; // Captura del rol seleccionado.
                $nombre = $_POST['nombre']; // Captura del nombre ingresado.

                // Validación básica
                if (empty($correo) || empty($contrasena)) { // Si el correo o la contraseña están vacíos, se muestra un error.
                    echo '<div class="alert alert-danger">El correo y la contraseña son obligatorios.</div>';
                } else { // Si se llenan los campos obligatorios:
                    try {
                        // Consulta SQL para insertar un nuevo usuario.
                        $sql = "INSERT INTO usuarios (correo, contrasena, rol_id, nombre) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql); // Se prepara la consulta para evitar inyecciones SQL.
                        if ($stmt->execute([$correo, password_hash($contrasena, PASSWORD_DEFAULT), $rol_id, $nombre])) {
                            // Si la consulta es exitosa, se muestra un mensaje de éxito.
                            echo '<div class="alert alert-success">Usuario creado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el usuario.</div>'; // Si falla, se muestra un error.
                        }
                    } catch (PDOException $e) { // Captura de errores de la base de datos.
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para agregar un nuevo usuario -->
            <h2 class="mb-4">Agregar Usuario</h2>
            <form method="POST" action="crud_usuarios.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required> <!-- Campo de correo obligatorio. -->
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required> <!-- Campo de contraseña obligatorio. -->
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select name="rol_id" class="form-select" required> <!-- Dropdown para seleccionar un rol. -->
                        <?php
                        // Obtener roles para el dropdown
                        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC); // Se obtiene la lista de roles desde la base de datos.
                        foreach ($roles as $rol) { // Se recorre cada rol para mostrarlo en el dropdown.
                            echo '<option value="' . $rol['id'] . '">' . $rol['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"> <!-- Campo opcional para el nombre. -->
                </div>
                <button type="submit" class="btn btn-primary">Agregar Usuario</button> <!-- Botón de envío. -->
            </form>
            <br>
            <a href="crud_usuarios.php" class="btn btn-secondary">Ver Usuarios</a> <!-- Botón para regresar a la lista de usuarios. -->
            <?php
            break;

        case 'read': // Caso para leer y mostrar la lista de usuarios.
            $sql = "SELECT u.*, r.nombre AS rol_nombre FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id"; // Consulta SQL para obtener usuarios junto con sus roles.
            $stmt = $pdo->query($sql); // Ejecutar la consulta.
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC); // Se obtienen todos los usuarios como un array asociativo.
            ?>
            <h2 class="mb-4">Lista de Usuarios</h2>
            <a href="crud_usuarios.php?action=create" class="btn btn-success mb-3">Agregar Usuario</a> <!-- Botón para agregar un nuevo usuario. -->
            <table class="table table-striped table-hover"> <!-- Tabla con diseño Bootstrap. -->
                <thead class="table-dark"> <!-- Cabecera de la tabla. -->
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
                <?php foreach ($usuarios as $usuario) { ?> <!-- Ciclo para mostrar cada usuario en una fila de la tabla. -->
                    <tr>
                        <td><?php echo $usuario['id']; ?></td> <!-- Mostrar ID. -->
                        <td><?php echo $usuario['correo']; ?></td> <!-- Mostrar correo. -->
                        <td><?php echo $usuario['nombre']; ?></td> <!-- Mostrar nombre. -->
                        <td><?php echo $usuario['rol_nombre']; ?></td> <!-- Mostrar nombre del rol. -->
                        <td><?php echo $usuario['fecha_registro']; ?></td> <!-- Mostrar fecha de registro. -->
                        <td>
                            <!-- Botones de acción: Editar o Eliminar. -->
                            <a href="crud_usuarios.php?action=update&id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update': // Caso para actualizar un usuario.
            $id = $_GET['id']; // Obtener el ID del usuario a actualizar desde la URL.
            $sql = "SELECT * FROM usuarios WHERE id = ?"; // Consulta SQL para obtener los datos del usuario.
            $stmt = $pdo->prepare($sql); // Preparar la consulta.
            $stmt->execute([$id]); // Ejecutar con el ID del usuario.
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener los datos del usuario.

            if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Si el formulario es enviado:
                $correo = $_POST['correo']; // Obtener el correo actualizado.
                $rol_id = $_POST['rol_id']; // Obtener el rol actualizado.
                $nombre = $_POST['nombre']; // Obtener el nombre actualizado.

                if (empty($correo)) { // Validación: el correo no puede estar vacío.
                    echo '<div class="alert alert-danger">El correo es obligatorio.</div>';
                } else {
                    try {
                        // Consulta SQL para actualizar el usuario.
                        $sql = "UPDATE usuarios SET correo = ?, rol_id = ?, nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$correo, $rol_id, $nombre, $id])) { // Ejecutar la actualización.
                            echo '<div class="alert alert-success">Usuario actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el usuario.</div>';
                        }
                    } catch (PDOException $e) { // Captura de errores de base de datos.
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para actualizar un usuario -->
            <h2 class="mb-4">Editar Usuario</h2>
            <form method="POST" action="crud_usuarios.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="<?php echo $usuario['correo']; ?>" required> <!-- Campo de correo con valor actual. -->
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select name="rol_id" class="form-select" required> <!-- Dropdown con roles disponibles y el actual seleccionado. -->
                        <?php
                        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC); // Obtener roles nuevamente.
                        foreach ($roles as $rol) {
                            $selected = ($rol['id'] == $usuario['rol_id']) ? 'selected' : ''; // Seleccionar el rol actual.
                            echo '<option value="' . $rol['id'] . '" ' . $selected . '>' . $rol['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>"> <!-- Campo de nombre con valor actual. -->
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button> <!-- Botón de actualización. -->
            </form>
            <br>
            <a href="crud_usuarios.php" class="btn btn-secondary">Volver a la lista</a> <!-- Botón para regresar a la lista de usuarios. -->
            <?php
            break;

        case 'delete': // Caso para eliminar un usuario.
            $id = $_GET['id']; // Obtener el ID del usuario a eliminar.
            $sql = "DELETE FROM usuarios WHERE id = ?"; // Consulta SQL para eliminar el usuario.
            $stmt = $pdo->prepare($sql); // Preparar la consulta.
            if ($stmt->execute([$id])) { // Ejecutar la eliminación.
                echo '<div class="alert alert-success">Usuario eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el usuario.</div>';
            }
            echo '<a href="crud_usuarios.php" class="btn btn-secondary">Volver a la lista</a>'; // Botón para regresar a la lista.
            break;

        default: // Caso por defecto si la acción no es válida.
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script> <!-- Inclusión del script de Bootstrap y dependencias. -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
