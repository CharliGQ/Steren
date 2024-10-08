<?php
// Incluir la conexión a la base de datos
include 'index.php';
require_once 'db.php';

// Definir la acción (crear, leer, actualizar, eliminar)
// Si no se define una acción en el GET, por defecto se usará 'read'
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Lista de Deseos</title>
    <!-- Incluir Bootstrap CSS desde una CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones según el parámetro 'action'
    switch ($action) {
        case 'create':
            // Acción para crear un nuevo elemento en la lista de deseos
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cliente_id = $_POST['cliente_id'];
                $producto_id = $_POST['producto_id'];

                // Validación básica: verificar que los campos no estén vacíos
                if (empty($cliente_id) || empty($producto_id)) {
                    echo '<div class="alert alert-danger">El cliente y el producto son obligatorios.</div>';
                } else {
                    try {
                        // Insertar el nuevo elemento en la base de datos
                        $sql = "INSERT INTO lista_deseos (cliente_id, producto_id) VALUES (?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$cliente_id, $producto_id])) {
                            echo '<div class="alert alert-success">Producto agregado a la lista de deseos exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el producto a la lista de deseos.</div>';
                        }
                    } catch (PDOException $e) {
                        // Mostrar error si la consulta falla
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para agregar un producto a la lista de deseos -->
            <h2 class="mb-4">Agregar a Lista de Deseos</h2>
            <form method="POST" action="crud_lista_deseos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <?php
                        // Obtener la lista de clientes desde la base de datos para el dropdown
                        $clientes = $pdo->query("SELECT id, nombre FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($clientes as $cliente) {
                            echo '<option value="' . $cliente['id'] . '">' . $cliente['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <?php
                        // Obtener la lista de productos desde la base de datos para el dropdown
                        $productos = $pdo->query("SELECT id, nombre FROM productos")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($productos as $producto) {
                            echo '<option value="' . $producto['id'] . '">' . $producto['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Agregar a Lista de Deseos</button>
            </form>
            <br>
            <!-- Enlace para volver a la lista de deseos -->
            <a href="crud_lista_deseos.php" class="btn btn-secondary">Ver Lista de Deseos</a>
            <?php
            break;

        case 'read':
            // Acción para leer la lista de deseos
            // Consulta para obtener los datos de la lista de deseos incluyendo los nombres de cliente y producto
            $sql = "SELECT ld.*, c.nombre AS cliente_nombre, p.nombre AS producto_nombre 
                    FROM lista_deseos ld 
                    JOIN clientes c ON ld.cliente_id = c.id 
                    JOIN productos p ON ld.producto_id = p.id";
            $stmt = $pdo->query($sql);
            $lista_deseos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <!-- Mostrar la lista de deseos -->
            <h2 class="mb-4">Lista de Deseos</h2>
            <a href="crud_lista_deseos.php?action=create" class="btn btn-success mb-3">Agregar a Lista de Deseos</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Fecha Agregado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($lista_deseos as $item) { ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['cliente_nombre']; ?></td>
                        <td><?php echo $item['producto_nombre']; ?></td>
                        <td><?php echo $item['fecha_agregado']; ?></td>
                        <td>
                            <!-- Enlace para eliminar un elemento de la lista de deseos -->
                            <a href="crud_lista_deseos.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'delete':
            // Acción para eliminar un elemento de la lista de deseos
            $id = $_GET['id']; // Obtener el ID del producto a eliminar
            // Eliminar el producto de la base de datos
            $sql = "DELETE FROM lista_deseos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Producto eliminado de la lista de deseos exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el producto de la lista de deseos.</div>';
            }
            // Enlace para volver a la lista de deseos
            echo '<a href="crud_lista_deseos.php" class="btn btn-secondary">Volver a la lista</a>';
            break;

        default:
            // Acción no válida, mostrar mensaje de error
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Incluir Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
