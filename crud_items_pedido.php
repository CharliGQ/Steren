<?php
// Incluir la conexión a la base de datos
include 'index.php';
require_once 'db.php';

// Definir la acción (crear, leer, actualizar, eliminar)
// Si no se define una acción, se mostrará la lista de items de pedido por defecto
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Incluir la cabecera HTML y la CDN de Bootstrap para el estilo visual
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Items de Pedido</title>
    <!-- Bootstrap CSS para el diseño responsivo y estilo-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Estructura de control para gestionar las acciones CRUD
    switch ($action) {
        case 'create': // Acción para crear un nuevo item de pedido
            // Si el método de solicitud es POST, procesamos el formulario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Recibir datos del formulario
                $pedido_id = $_POST['pedido_id'];
                $producto_id = $_POST['producto_id'];
                $cantidad = $_POST['cantidad'];
                $precio = $_POST['precio'];

                // Validación básica de los campos
                if (empty($pedido_id) || empty($producto_id) || empty($cantidad) || empty($precio)) {
                    // Mostrar un mensaje de error si hay campos vacíos
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        // Insertar un nuevo item de pedido en la base de datos
                        $sql = "INSERT INTO items_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        // Ejecutar la consulta
                        if ($stmt->execute([$pedido_id, $producto_id, $cantidad, $precio])) {
                            // Mostrar mensaje de éxito si se ejecuta correctamente
                            echo '<div class="alert alert-success">Item del pedido creado exitosamente.</div>';
                        } else {
                            // Mostrar mensaje de error si la consulta falla
                            echo '<div class="alert alert-danger">Error al crear el item del pedido.</div>';
                        }
                    } catch (PDOException $e) {
                        // Capturar y mostrar errores de la base de datos
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para crear un nuevo item de pedido -->
            <h2 class="mb-4">Crear Item de Pedido</h2>
            <form method="POST" action="crud_items_pedido.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="producto_id" class="form-label">ID del Producto</label>
                    <input type="number" name="producto_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Item</button>
            </form>
            <br>
            <!-- Botón para regresar a la lista de items -->
            <a href="crud_items_pedido.php" class="btn btn-secondary">Ver items</a>
            <?php
            break;

        case 'read': // Acción para leer y mostrar todos los items
            // Consultar todos los items de la base de datos
            $sql = "SELECT * FROM items_pedido";
            $stmt = $pdo->query($sql);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener los resultados
            ?>
            <!-- Mostrar la lista de items -->
            <h2 class="mb-4">Lista de Items de Pedido</h2>
            <a href="crud_items_pedido.php?action=create" class="btn btn-success mb-3">Crear Item</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID del Pedido</th>
                    <th>ID del Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['pedido_id']; ?></td>
                        <td><?php echo $item['producto_id']; ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td><?php echo $item['precio']; ?></td>
                        <td>
                            <!-- Botón para editar el item -->
                            <a href="crud_items_pedido.php?action=update&id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <!-- Botón para eliminar el item con confirmación -->
                            <a href="crud_items_pedido.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update': // Acción para actualizar un item
            // Obtener el ID del item a editar
            $id = $_GET['id'];
            // Consultar el item seleccionado
            $sql = "SELECT * FROM items_pedido WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener los datos del item

            // Si el formulario es enviado
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener los valores del formulario
                $pedido_id = $_POST['pedido_id'];
                $producto_id = $_POST['producto_id'];
                $cantidad = $_POST['cantidad'];
                $precio = $_POST['precio'];

                // Validación básica
                if (empty($pedido_id) || empty($producto_id) || empty($cantidad) || empty($precio)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        // Actualizar el item en la base de datos
                        $sql = "UPDATE items_pedido SET pedido_id = ?, producto_id = ?, cantidad = ?, precio = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        // Ejecutar la actualización
                        if ($stmt->execute([$pedido_id, $producto_id, $cantidad, $precio, $id])) {
                            echo '<div class="alert alert-success">Item actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el item del pedido.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para editar un item de pedido -->
            <h2 class="mb-4">Editar Item de Pedido</h2>
            <form method="POST" action="crud_items_pedido.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido</label>
                    <input type="number" name="pedido_id" value="<?php echo $item['pedido_id']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="producto_id" class="form-label">ID del Producto</label>
                    <input type="number" name="producto_id" value="<?php echo $item['producto_id']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $item['precio']; ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Item</button>
            </form>
            <br>
            <!-- Botón para regresar a la lista de items -->
            <a href="crud_items_pedido.php" class="btn btn-secondary">Ver items</a>
            <?php
            break;

        case 'delete': // Acción para eliminar un item
            // Obtener el ID del item a eliminar
            $id = $_GET['id'];
            try {
                // Eliminar el item de la base de datos
                $sql = "DELETE FROM items_pedido WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                // Si la eliminación es exitosa, redirigir a la página principal
                if ($stmt->execute([$id])) {
                    header("Location: crud_items_pedido.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el item del pedido.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            break;
    }
    ?>
</div>

<!-- Bootstrap JS para funcionalidades interactivas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
