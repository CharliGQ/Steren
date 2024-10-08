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
    <title>CRUD Pagos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones (create, read, update, delete)
    switch ($action) {
        case 'create':
            // Crear un nuevo pago
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener datos del formulario
                $pedido_id = $_POST['pedido_id']; // ID del pedido asociado
                $metodo_pago_id = $_POST['metodo_pago_id']; // ID del método de pago utilizado
                $monto_pagado = $_POST['monto_pagado']; // Monto pagado por el cliente
                $estado = $_POST['estado']; // Estado del pago (ej: pagado, pendiente)

                // Validación de campos obligatorios
                if (empty($pedido_id) || empty($metodo_pago_id) || empty($monto_pagado) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Consulta SQL para insertar un nuevo pago en la base de datos
                        $sql = "INSERT INTO pagos (pedido_id, metodo_pago_id, monto_pagado, estado) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql); // Preparar la consulta SQL
                        if ($stmt->execute([$pedido_id, $metodo_pago_id, $monto_pagado, $estado])) {
                            echo '<div class="alert alert-success">Pago agregado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el pago.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Mostrar mensaje de error en caso de fallo en la base de datos
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Pago</h2>
            <!-- Formulario para agregar un nuevo pago -->
            <form method="POST" action="crud_pagos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" required> <!-- Campo para el ID del pedido -->
                </div>
                <div class="mb-3">
                    <label for="metodo_pago_id" class="form-label">ID del Método de Pago</label>
                    <input type="number" name="metodo_pago_id" class="form-control" required> <!-- Campo para el ID del método de pago -->
                </div>
                <div class="mb-3">
                    <label for="monto_pagado" class="form-label">Monto Pagado</label>
                    <input type="number" step="0.01" name="monto_pagado" class="form-control" required> <!-- Campo para el monto pagado -->
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" required> <!-- Campo para el estado del pago -->
                </div>
                <button type="submit" class="btn btn-primary">Agregar Pago</button> <!-- Botón para agregar el pago -->
            </form>
            <br>
            <a href="crud_pagos.php" class="btn btn-secondary">Ver pagos</a> <!-- Enlace para ver la lista de pagos -->
            <?php
            break;

        case 'read':
            // Leer y mostrar todos los pagos
            $sql = "SELECT p.*, mp.nombre_metodo FROM pagos p JOIN metodos_pago mp ON p.metodo_pago_id = mp.id"; // Consulta para obtener los pagos con el método de pago
            $stmt = $pdo->query($sql); // Ejecutar la consulta
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener los resultados
            ?>
            <h2 class="mb-4">Lista de Pagos</h2>
            <a href="crud_pagos.php?action=create" class="btn btn-success mb-3">Agregar Pago</a> <!-- Enlace para agregar un nuevo pago -->
            <!-- Tabla para mostrar la lista de pagos -->
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID del Pedido</th>
                    <th>Método de Pago</th>
                    <th>Monto Pagado</th>
                    <th>Fecha de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagos as $pago) { ?> <!-- Iterar sobre los pagos obtenidos -->
                    <tr>
                        <td><?php echo $pago['id']; ?></td> <!-- Mostrar ID del pago -->
                        <td><?php echo $pago['pedido_id']; ?></td> <!-- Mostrar ID del pedido -->
                        <td><?php echo $pago['nombre_metodo']; ?></td> <!-- Mostrar el nombre del método de pago -->
                        <td><?php echo $pago['monto_pagado']; ?></td> <!-- Mostrar el monto pagado -->
                        <td><?php echo $pago['fecha_pago']; ?></td> <!-- Mostrar la fecha del pago -->
                        <td><?php echo $pago['estado']; ?></td> <!-- Mostrar el estado del pago -->
                        <td>
                            <a href="crud_pagos.php?action=update&id=<?php echo $pago['id']; ?>" class="btn btn-warning btn-sm">Editar</a> <!-- Botón para editar -->
                            <a href="crud_pagos.php?action=delete&id=<?php echo $pago['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a> <!-- Botón para eliminar -->
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar un pago existente
            $id = $_GET['id']; // Obtener el ID del pago a actualizar
            $sql = "SELECT * FROM pagos WHERE id = ?"; // Consulta para obtener el pago por ID
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener el pago como array

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener datos actualizados del formulario
                $pedido_id = $_POST['pedido_id'];
                $metodo_pago_id = $_POST['metodo_pago_id'];
                $monto_pagado = $_POST['monto_pagado'];
                $estado = $_POST['estado'];

                // Validación de campos
                if (empty($pedido_id) || empty($metodo_pago_id) || empty($monto_pagado) || empty($estado)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Actualizar los datos del pago en la base de datos
                        $sql = "UPDATE pagos SET pedido_id = ?, metodo_pago_id = ?, monto_pagado = ?, estado = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql); // Preparar la consulta SQL
                        if ($stmt->execute([$pedido_id, $metodo_pago_id, $monto_pagado, $estado, $id])) {
                            echo '<div class="alert alert-success">Pago actualizado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el pago.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Mostrar mensaje de error en caso de fallo
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Pago</h2>
            <!-- Formulario para actualizar un pago -->
            <form method="POST" action="crud_pagos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" value="<?php echo $pago['pedido_id']; ?>" required> <!-- Campo para el ID del pedido -->
                </div>
                <div class="mb-3">
                    <label for="metodo_pago_id" class="form-label">ID del Método de Pago</label>
                    <input type="number" name="metodo_pago_id" class="form-control" value="<?php echo $pago['metodo_pago_id']; ?>" required> <!-- Campo para el ID del método de pago -->
                </div>
                <div class="mb-3">
                    <label for="monto_pagado" class="form-label">Monto Pagado</label>
                    <input type="number" step="0.01" name="monto_pagado" class="form-control" value="<?php echo $pago['monto_pagado']; ?>" required> <!-- Campo para el monto pagado -->
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control" value="<?php echo $pago['estado']; ?>" required> <!-- Campo para el estado del pago -->
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Pago</button> <!-- Botón para actualizar el pago -->
            </form>
            <br>
            <a href="crud_pagos.php" class="btn btn-secondary">Ver pagos</a> <!-- Enlace para ver la lista de pagos -->
            <?php
            break;

        case 'delete':
            // Eliminar un pago
            $id = $_GET['id']; // Obtener el ID del pago a eliminar
            try {
                // Eliminar el pago de la base de datos
                $sql = "DELETE FROM pagos WHERE id = ?";
                $stmt = $pdo->prepare($sql); // Preparar la consulta
                if ($stmt->execute([$id])) {
                    header("Location: crud_pagos.php?action=read"); // Redireccionar a la lista de pagos
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el pago.</div>'; // Mensaje de error
                }
            } catch (PDOException $e) {
                // Mostrar mensaje de error en caso de fallo
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
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
