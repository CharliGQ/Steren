<?php
// Incluir la conexión a la base de datos
require_once 'db.php';

// Definir la acción (crear, leer, actualizar, eliminar)
$action = isset($_GET['action']) ? $_GET['action'] : 'read';

// Obtener clientes para el dropdown
$sql = "SELECT * FROM clientes";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos para el dropdown
$sql = "SELECT * FROM productos";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluir la cabecera HTML y la CDN de Bootstrap
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CRUD Carrito de Compras</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear entrada en el carrito
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cliente_id = $_POST['cliente_id'];
                $producto_id = $_POST['producto_id'];
                $cantidad = $_POST['cantidad'];

                // Validación básica
                if (empty($cliente_id) || empty($producto_id) || empty($cantidad)) {
                    echo '<div class="alert alert-danger">Los campos cliente, producto y cantidad son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO carrito_compras (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$cliente_id, $producto_id, $cantidad])) {
                            echo '<div class="alert alert-success">Producto agregado al carrito exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el producto al carrito.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar al Carrito</h2>
            <form method="POST" action="crud_carrito_compras.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clientes as $cliente) { ?>
                            <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto) { ?>
                            <option value="<?php echo $producto['id']; ?>"><?php echo $producto['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar al Carrito</button>
            </form>
            <br>
            <a href="crud_carrito_compras.php" class="btn btn-secondary">Ver carrito</a>
            <?php
            break;

        case 'read':
            // Leer carrito de compras
            $sql = "SELECT cc.*, c.nombre AS cliente_nombre, p.nombre AS producto_nombre FROM carrito_compras cc 
                    LEFT JOIN clientes c ON cc.cliente_id = c.id 
                    LEFT JOIN productos p ON cc.producto_id = p.id";
            $stmt = $pdo->query($sql);
            $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Carrito de Compras</h2>
            <a href="crud_carrito_compras.php?action=create" class="btn btn-success mb-3">Agregar Producto</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Fecha Agregado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($carrito as $item) { ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['cliente_nombre']; ?></td>
                        <td><?php echo $item['producto_nombre']; ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td><?php echo $item['fecha_agregado']; ?></td>
                        <td>
                            <a href="crud_carrito_compras.php?action=update&id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_carrito_compras.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar entrada en el carrito
            $id = $_GET['id'];
            $sql = "SELECT * FROM carrito_compras WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener clientes para el dropdown
            $sql = "SELECT * FROM clientes";
            $stmt = $pdo->query($sql);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener productos para el dropdown
            $sql = "SELECT * FROM productos";
            $stmt = $pdo->query($sql);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cliente_id = $_POST['cliente_id'];
                $producto_id = $_POST['producto_id'];
                $cantidad = $_POST['cantidad'];

                if (empty($cliente_id) || empty($producto_id) || empty($cantidad)) {
                    echo '<div class="alert alert-danger">Los campos cliente, producto y cantidad son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE carrito_compras SET cliente_id = ?, producto_id = ?, cantidad = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$cliente_id, $producto_id, $cantidad, $id])) {
                            echo '<div class="alert alert-success">Carrito actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el carrito.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Carrito</h2>
            <form method="POST" action="crud_carrito_compras.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clientes as $cliente) { ?>
                            <option value="<?php echo $cliente['id']; ?>" <?php if ($cliente['id'] == $item['cliente_id']) echo 'selected'; ?>>
                                <?php echo $cliente['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto) { ?>
                            <option value="<?php echo $producto['id']; ?>" <?php if ($producto['id'] == $item['producto_id']) echo 'selected'; ?>>
                                <?php echo $producto['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" value="<?php echo $item['cantidad']; ?>" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Carrito</button>
            </form>
            <br>
            <a href="crud_carrito_compras.php" class="btn btn-secondary">Ver carrito</a>
            <?php
            break;

        case 'delete':
            // Eliminar entrada en el carrito
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM carrito_compras WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_carrito_compras.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el carrito.</div>';
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
