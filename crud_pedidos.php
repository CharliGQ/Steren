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
    <title>CRUD Pedidos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear pedido
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener datos del formulario
                $nombre_cliente = $_POST['nombre_cliente'];
                $monto_total = $_POST['monto_total'];

                // Validación básica
                if (empty($nombre_cliente) || empty($monto_total)) {
                    echo '<div class="alert alert-danger">El nombre del cliente y el monto total son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Preparar la consulta SQL para insertar el nuevo pedido
                        $sql = "INSERT INTO pedidos (nombre_cliente, monto_total) VALUES (?, ?)"; // Consulta de inserción
                        $stmt = $pdo->prepare($sql); // Prepara la consulta
                        if ($stmt->execute([$nombre_cliente, $monto_total])) {
                            echo '<div class="alert alert-success">Pedido creado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el pedido.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Captura de excepciones
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Pedido</h2>
            <!-- Formulario para crear un nuevo pedido -->
            <form method="POST" action="crud_pedidos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" class="form-control" required> <!-- Campo para el nombre del cliente -->
                </div>
                <div class="mb-3">
                    <label for="monto_total" class="form-label">Monto Total</label>
                    <input type="number" step="0.01" name="monto_total" class="form-control" required> <!-- Campo para el monto total -->
                </div>
                <button type="submit" class="btn btn-primary">Crear Pedido</button> <!-- Botón para enviar el formulario -->
            </form>
            <br>
            <a href="crud_pedidos.php" class="btn btn-secondary">Ver pedidos</a> <!-- Enlace para ver los pedidos -->
            <?php
            break;

        case 'read':
            // Leer pedidos
            $sql = "SELECT * FROM pedidos"; // Consulta para seleccionar todos los pedidos
            $stmt = $pdo->query($sql); // Ejecutar la consulta
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los pedidos como un array asociativo
            ?>
            <h2 class="mb-4">Lista de Pedidos</h2>
            <a href="crud_pedidos.php?action=create" class="btn btn-success mb-3">Crear Pedido</a> <!-- Enlace para crear un nuevo pedido -->
            <table class="table table-striped table-hover"> <!-- Tabla para mostrar los pedidos -->
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha Pedido</th>
                    <th>Nombre Cliente</th>
                    <th>Monto Total</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $pedido) { ?> <!-- Iterar sobre los pedidos -->
                    <tr>
                        <td><?php echo $pedido['id']; ?></td> <!-- Mostrar ID del pedido -->
                        <td><?php echo $pedido['fecha_pedido']; ?></td> <!-- Mostrar fecha del pedido -->
                        <td><?php echo $pedido['nombre_cliente']; ?></td> <!-- Mostrar nombre del cliente -->
                        <td><?php echo $pedido['monto_total']; ?></td> <!-- Mostrar monto total -->
                        <td>
                            <a href="crud_pedidos.php?action=update&id=<?php echo $pedido['id']; ?>" class="btn btn-warning btn-sm">Editar</a> <!-- Botón para editar -->
                            <a href="crud_pedidos.php?action=delete&id=<?php echo $pedido['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a> <!-- Botón para eliminar -->
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar pedido
            $id = $_GET['id']; // Obtener el ID del pedido a actualizar
            $sql = "SELECT * FROM pedidos WHERE id = ?"; // Consulta para seleccionar el pedido por ID
            $stmt = $pdo->prepare($sql); // Preparar la consulta
            $stmt->execute([$id]); // Ejecutar la consulta con el ID
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener el pedido como un array asociativo

            if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica si se ha enviado el formulario
                // Obtener datos del formulario
                $nombre_cliente = $_POST['nombre_cliente'];
                $monto_total = $_POST['monto_total'];

                // Validación básica
                if (empty($nombre_cliente) || empty($monto_total)) {
                    echo '<div class="alert alert-danger">El nombre del cliente y el monto total son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Preparar la consulta SQL para actualizar el pedido
                        $sql = "UPDATE pedidos SET nombre_cliente = ?, monto_total = ? WHERE id = ?"; // Consulta de actualización
                        $stmt = $pdo->prepare($sql); // Prepara la consulta
                        if ($stmt->execute([$nombre_cliente, $monto_total, $id])) {
                            echo '<div class="alert alert-success">Pedido actualizado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el pedido.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Captura de excepciones
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Pedido</h2>
            <!-- Formulario para editar el pedido -->
            <form method="POST" action="crud_pedidos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" value="<?php echo $pedido['nombre_cliente']; ?>" class="form-control" required> <!-- Campo para el nombre del cliente -->
                </div>
                <div class="mb-3">
                    <label for="monto_total" class="form-label">Monto Total</label>
                    <input type="number" step="0.01" name="monto_total" value="<?php echo $pedido['monto_total']; ?>" class="form-control" required> <!-- Campo para el monto total -->
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Pedido</button> <!-- Botón para enviar el formulario -->
            </form>
            <br>
            <a href="crud_pedidos.php" class="btn btn-secondary">Ver pedidos</a> <!-- Enlace para ver los pedidos -->
            <?php
            break;

        case 'delete':
            // Eliminar pedido
            $id = $_GET['id']; // Obtener el ID del pedido a eliminar
            try {
                // Preparar la consulta SQL para eliminar el pedido
                $sql = "DELETE FROM pedidos WHERE id = ?"; // Consulta de eliminación
                $stmt = $pdo->prepare($sql); // Prepara la consulta
                if ($stmt->execute([$id])) {
                    header("Location: crud_pedidos.php?action=read"); // Redirigir después de eliminar
                    exit; // Detener la ejecución
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el pedido.</div>'; // Mensaje de error
                }
            } catch (PDOException $e) {
                // Captura de excepciones
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
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
