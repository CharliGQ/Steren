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
    <title>CRUD Historial Inventario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones basado en el valor de $action
    switch ($action) {
        case 'create': // Crear nuevo registro en el historial de inventario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Captura los datos del formulario
                $producto_id = $_POST['producto_id'];
                $cantidad_ajustada = $_POST['cantidad_ajustada'];
                $tipo_ajuste = $_POST['tipo_ajuste'];
                $descripcion = $_POST['descripcion'];

                // Validación básica: Verifica que los campos obligatorios no estén vacíos
                if (empty($producto_id) || empty($cantidad_ajustada) || empty($tipo_ajuste)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        // Inserta el nuevo registro en la base de datos
                        $sql = "INSERT INTO historial_inventario (producto_id, cantidad_ajustada, tipo_ajuste, descripcion) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$producto_id, $cantidad_ajustada, $tipo_ajuste, $descripcion])) {
                            echo '<div class="alert alert-success">Ajuste de inventario registrado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al registrar el ajuste de inventario.</div>';
                        }
                    } catch (PDOException $e) {
                        // Muestra el mensaje de error en caso de fallo
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para crear un nuevo ajuste de inventario -->
            <h2 class="mb-4">Agregar Ajuste de Inventario</h2>
            <form method="POST" action="crud_historial_inventario.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php
                        // Consultar la lista de productos para mostrar en el select
                        $sql = "SELECT id, nombre FROM productos";
                        $stmt = $pdo->query($sql);
                        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($productos as $producto) {
                            echo "<option value=\"{$producto['id']}\">{$producto['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad_ajustada" class="form-label">Cantidad Ajustada</label>
                    <input type="number" name="cantidad_ajustada" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_ajuste" class="form-label">Tipo de Ajuste</label>
                    <select name="tipo_ajuste" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Agregar Ajuste</button>
            </form>
            <br>
            <a href="crud_historial_inventario.php" class="btn btn-secondary">Ver historial de inventario</a>
            <?php
            break;

        case 'read': // Leer y mostrar el historial de inventario
            $sql = "SELECT hi.id, p.nombre AS producto_nombre, hi.cantidad_ajustada, hi.tipo_ajuste, hi.fecha_ajuste, hi.descripcion 
                    FROM historial_inventario hi 
                    JOIN productos p ON hi.producto_id = p.id";
            $stmt = $pdo->query($sql);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Historial de Inventario</h2>
            <a href="crud_historial_inventario.php?action=create" class="btn btn-success mb-3">Agregar Ajuste</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad Ajustada</th>
                    <th>Tipo de Ajuste</th>
                    <th>Fecha de Ajuste</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($historial as $registro) { ?>
                    <tr>
                        <td><?php echo $registro['id']; ?></td>
                        <td><?php echo $registro['producto_nombre']; ?></td>
                        <td><?php echo $registro['cantidad_ajustada']; ?></td>
                        <td><?php echo $registro['tipo_ajuste']; ?></td>
                        <td><?php echo $registro['fecha_ajuste']; ?></td>
                        <td><?php echo $registro['descripcion']; ?></td>
                        <td>
                            <!-- Botones para editar o eliminar un ajuste -->
                            <a href="crud_historial_inventario.php?action=update&id=<?php echo $registro['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_historial_inventario.php?action=delete&id=<?php echo $registro['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update': // Actualizar un registro del historial de inventario
            $id = $_GET['id'];
            $sql = "SELECT * FROM historial_inventario WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $producto_id = $_POST['producto_id'];
                $cantidad_ajustada = $_POST['cantidad_ajustada'];
                $tipo_ajuste = $_POST['tipo_ajuste'];
                $descripcion = $_POST['descripcion'];

                if (empty($producto_id) || empty($cantidad_ajustada) || empty($tipo_ajuste)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        // Actualiza el registro en la base de datos
                        $sql = "UPDATE historial_inventario SET producto_id = ?, cantidad_ajustada = ?, tipo_ajuste = ?, descripcion = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$producto_id, $cantidad_ajustada, $tipo_ajuste, $descripcion, $id])) {
                            echo '<div class="alert alert-success">Ajuste de inventario actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el ajuste de inventario.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <!-- Formulario para editar un ajuste de inventario -->
            <h2 class="mb-4">Editar Ajuste de Inventario</h2>
            <form method="POST" action="crud_historial_inventario.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <?php
                        // Obtener productos para el select
                        $sql = "SELECT id, nombre FROM productos";
                        $stmt = $pdo->query($sql);
                        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($productos as $producto) {
                            $selected = $producto['id'] == $registro['producto_id'] ? 'selected' : '';
                            echo "<option value=\"{$producto['id']}\" $selected>{$producto['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad_ajustada" class="form-label">Cantidad Ajustada</label>
                    <input type="number" name="cantidad_ajustada" class="form-control" value="<?php echo $registro['cantidad_ajustada']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_ajuste" class="form-label">Tipo de Ajuste</label>
                    <select name="tipo_ajuste" class="form-select" required>
                        <option value="entrada" <?php echo $registro['tipo_ajuste'] == 'entrada' ? 'selected' : ''; ?>>Entrada</option>
                        <option value="salida" <?php echo $registro['tipo_ajuste'] == 'salida' ? 'selected' : ''; ?>>Salida</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" value="<?php echo $registro['descripcion']; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Ajuste</button>
            </form>
            <br>
            <a href="crud_historial_inventario.php" class="btn btn-secondary">Ver historial de inventario</a>
            <?php
            break;

        case 'delete': // Eliminar un registro del historial de inventario
            $id = $_GET['id'];
            try {
                // Elimina el registro de la base de datos
                $sql = "DELETE FROM historial_inventario WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    echo '<div class="alert alert-success">Ajuste de inventario eliminado exitosamente.</div>';
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el ajuste de inventario.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            echo '<br><a href="crud_historial_inventario.php" class="btn btn-secondary">Ver historial de inventario</a>';
            break;

        default: // Caso por defecto: Leer el historial de inventario
            header('Location: crud_historial_inventario.php?action=read');
            break;
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
