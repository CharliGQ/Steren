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
    <title>CRUD Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear producto
            if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica si se ha enviado el formulario
                // Obtener datos del formulario
                $nombre = $_POST['nombre'];
                $categoria = $_POST['categoria'];
                $precio = $_POST['precio'];
                $stock = $_POST['stock'];
                $descripcion = $_POST['descripcion'];

                // Validación de datos básicos
                if (empty($nombre) || empty($categoria) || empty($precio) || empty($stock)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Preparar la consulta SQL para insertar el producto
                        $sql = "INSERT INTO productos (nombre, categoria, precio, stock, descripcion) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql); // Prepara la consulta
                        if ($stmt->execute([$nombre, $categoria, $precio, $stock, $descripcion])) {
                            echo '<div class="alert alert-success">Producto creado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al crear el producto.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Captura de excepciones
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Producto</h2>
            <!-- Formulario para crear un nuevo producto -->
            <form method="POST" action="crud_productos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <!-- Campo para el nombre del producto -->
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para la categoría -->
                    <label for="categoria" class="form-label">Categoría</label>
                    <input type="text" name="categoria" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para el precio -->
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para el stock -->
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para la descripción -->
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control"></textarea> 
                </div>
                <!-- Botón para enviar el formulario -->
                <button type="submit" class="btn btn-primary">Crear Producto</button> 
            </form>
            <br>
            <!-- Enlace para ver productos -->
            <a href="crud_productos.php" class="btn btn-secondary">Ver productos</a> 
            <?php
            break;

        case 'read':
            // Leer productos
            $sql = "SELECT * FROM productos"; // Consulta para seleccionar todos los productos
            $stmt = $pdo->query($sql); // Ejecutar la consulta
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los productos como un array asociativo
            ?>
            <h2 class="mb-4">Lista de Productos</h2>
            <!-- Enlace para crear un nuevo producto -->
            <a href="crud_productos.php?action=create" class="btn btn-success mb-3">Crear Producto</a> 
            <!-- Tabla para mostrar los productos existentes -->
            <table class="table table-striped table-hover"> 
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ciclo para recorrer cada producto y mostrarlo en la tabla -->
                <?php foreach ($productos as $producto) { ?> 
                    <tr>
                        <td><?php echo $producto['id']; ?></td>
                        <td><?php echo $producto['nombre']; ?></td>
                        <td><?php echo $producto['categoria']; ?></td>
                        <td><?php echo $producto['precio']; ?></td>
                        <td><?php echo $producto['stock']; ?></td>
                        <td><?php echo $producto['descripcion']; ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <a href="crud_productos.php?action=update&id=<?php echo $producto['id']; ?>" class="btn btn-warning btn-sm">Editar</a> 
                            <!-- Botón para eliminar -->
                            <a href="crud_productos.php?action=delete&id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a> 
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar producto
            $id = $_GET['id']; // Obtener el ID del producto a actualizar
            $sql = "SELECT * FROM productos WHERE id = ?"; // Consulta para seleccionar el producto por ID
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]); // Ejecutar la consulta con el ID
            $producto = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener el producto como un array asociativo

            if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica si se ha enviado el formulario
                // Obtener datos del formulario
                $nombre = $_POST['nombre'];
                $categoria = $_POST['categoria'];
                $precio = $_POST['precio'];
                $stock = $_POST['stock'];
                $descripcion = $_POST['descripcion'];

                // Validación de datos básicos
                if (empty($nombre) || empty($categoria) || empty($precio) || empty($stock)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>'; // Mensaje de error
                } else {
                    try {
                        // Preparar la consulta SQL para actualizar el producto
                        $sql = "UPDATE productos SET nombre = ?, categoria = ?, precio = ?, stock = ?, descripcion = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql); // Prepara la consulta
                        if ($stmt->execute([$nombre, $categoria, $precio, $stock, $descripcion, $id])) {
                            echo '<div class="alert alert-success">Producto actualizado exitosamente.</div>'; // Mensaje de éxito
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el producto.</div>'; // Mensaje de error
                        }
                    } catch (PDOException $e) {
                        // Captura de excepciones
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Producto</h2>
            <!-- Formulario para editar el producto -->
            <form method="POST" action="crud_productos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <!-- Campo para el nombre del producto -->
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para la categoría -->
                    <label for="categoria" class="form-label">Categoría</label>
                    <input type="text" name="categoria" value="<?php echo $producto['categoria']; ?>" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para el precio -->
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para el stock -->
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" class="form-control" required> 
                </div>
                <div class="mb-3">
                    <!-- Campo para la descripción -->
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control"><?php echo $producto['descripcion']; ?></textarea> 
                </div>
                <!-- Botón para enviar el formulario -->
                <button type="submit" class="btn btn-primary">Actualizar Producto</button> 
            </form>
            <br>
            <!-- Enlace para ver productos -->
            <a href="crud_productos.php" class="btn btn-secondary">Ver productos</a> 
            <?php
            break;

        case 'delete':
            // Eliminar producto
            $id = $_GET['id']; // Obtener el ID del producto a eliminar
            try {
                // Preparar la consulta SQL para eliminar el producto
                $sql = "DELETE FROM productos WHERE id = ?";
                $stmt = $pdo->prepare($sql); // Prepara la consulta
                if ($stmt->execute([$id])) {
                    echo '<div class="alert alert-success">Producto eliminado exitosamente.</div>'; // Mensaje de éxito
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el producto.</div>'; // Mensaje de error
                }
            } catch (PDOException $e) {
                // Captura de excepciones
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>'; // Mensaje de error
            }
            ?>
            <!-- Enlace para ver productos -->
            <a href="crud_productos.php" class="btn btn-secondary">Ver productos</a> 
            <?php
            break;
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
