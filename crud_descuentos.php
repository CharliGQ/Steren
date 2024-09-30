<?php
// Incluir la conexión a la base de datos
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
    <title>CRUD Descuentos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear descuento
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_descuento = $_POST['nombre_descuento'];
                $tipo_descuento = $_POST['tipo_descuento'];
                $valor = $_POST['valor'];
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];

                // Validación básica
                if (empty($nombre_descuento) || empty($tipo_descuento) || empty($valor)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO descuentos (nombre_descuento, tipo_descuento, valor, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_descuento, $tipo_descuento, $valor, $fecha_inicio, $fecha_fin])) {
                            echo '<div class="alert alert-success">Descuento agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el descuento.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Descuento</h2>
            <form method="POST" action="crud_descuentos.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_descuento" class="form-label">Nombre del Descuento</label>
                    <input type="text" name="nombre_descuento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_descuento" class="form-label">Tipo de Descuento</label>
                    <select name="tipo_descuento" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="porcentaje">Porcentaje</option>
                        <option value="cantidad_fija">Cantidad Fija</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="valor" class="form-label">Valor</label>
                    <input type="number" name="valor" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="datetime-local" name="fecha_inicio" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="datetime-local" name="fecha_fin" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Agregar Descuento</button>
            </form>
            <br>
            <a href="crud_descuentos.php" class="btn btn-secondary">Ver descuentos</a>
            <?php
            break;

        case 'read':
            // Leer descuentos
            $sql = "SELECT * FROM descuentos";
            $stmt = $pdo->query($sql);
            $descuentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Descuentos</h2>
            <a href="crud_descuentos.php?action=create" class="btn btn-success mb-3">Agregar Descuento</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Descuento</th>
                    <th>Tipo de Descuento</th>
                    <th>Valor</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($descuentos as $descuento) { ?>
                    <tr>
                        <td><?php echo $descuento['id']; ?></td>
                        <td><?php echo $descuento['nombre_descuento']; ?></td>
                        <td><?php echo $descuento['tipo_descuento']; ?></td>
                        <td><?php echo $descuento['valor']; ?></td>
                        <td><?php echo $descuento['fecha_inicio']; ?></td>
                        <td><?php echo $descuento['fecha_fin']; ?></td>
                        <td>
                            <a href="crud_descuentos.php?action=update&id=<?php echo $descuento['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_descuentos.php?action=delete&id=<?php echo $descuento['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar descuento
            $id = $_GET['id'];
            $sql = "SELECT * FROM descuentos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $descuento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_descuento = $_POST['nombre_descuento'];
                $tipo_descuento = $_POST['tipo_descuento'];
                $valor = $_POST['valor'];
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];

                if (empty($nombre_descuento) || empty($tipo_descuento) || empty($valor)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE descuentos SET nombre_descuento = ?, tipo_descuento = ?, valor = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_descuento, $tipo_descuento, $valor, $fecha_inicio, $fecha_fin, $id])) {
                            echo '<div class="alert alert-success">Descuento actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el descuento.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Descuento</h2>
            <form method="POST" action="crud_descuentos.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_descuento" class="form-label">Nombre del Descuento</label>
                    <input type="text" name="nombre_descuento" class="form-control" value="<?php echo $descuento['nombre_descuento']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_descuento" class="form-label">Tipo de Descuento</label>
                    <select name="tipo_descuento" class="form-select" required>
                        <option value="<?php echo $descuento['tipo_descuento']; ?>" selected><?php echo $descuento['tipo_descuento']; ?></option>
                        <option value="porcentaje">Porcentaje</option>
                        <option value="cantidad_fija">Cantidad Fija</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="valor" class="form-label">Valor</label>
                    <input type="number" name="valor" class="form-control" step="0.01" value="<?php echo $descuento['valor']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="datetime-local" name="fecha_inicio" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($descuento['fecha_inicio'])); ?>">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="datetime-local" name="fecha_fin" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($descuento['fecha_fin'])); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Descuento</button>
            </form>
            <br>
            <a href="crud_descuentos.php" class="btn btn-secondary">Ver descuentos</a>
            <?php
            break;

        case 'delete':
            // Eliminar descuento
            $id = $_GET['id'];
            $sql = "DELETE FROM descuentos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Descuento eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el descuento.</div>';
            }
            echo '<a href="crud_descuentos.php" class="btn btn-secondary">Volver a la lista</a>';
            break;

        default:
            // Acción no válida
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
