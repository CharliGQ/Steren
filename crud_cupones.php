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
    <title>CRUD Cupones</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear cupón
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $codigo = $_POST['codigo'];
                $descuento_id = $_POST['descuento_id'];

                // Validación básica
                if (empty($codigo) || empty($descuento_id)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO cupones (codigo, descuento_id) VALUES (?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$codigo, $descuento_id])) {
                            echo '<div class="alert alert-success">Cupón agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el cupón.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Cupón</h2>
            <form method="POST" action="crud_cupones.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="descuento_id" class="form-label">Descuento</label>
                    <select name="descuento_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php
                        // Obtener descuentos para el select
                        $sql = "SELECT id, nombre_descuento FROM descuentos";
                        $stmt = $pdo->query($sql);
                        $descuentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($descuentos as $descuento) {
                            echo "<option value=\"{$descuento['id']}\">{$descuento['nombre_descuento']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Cupón</button>
            </form>
            <br>
            <a href="crud_cupones.php" class="btn btn-secondary">Ver cupones</a>
            <?php
            break;

        case 'read':
            // Leer cupones
            $sql = "SELECT cu.id, cu.codigo, cu.usado, d.nombre_descuento FROM cupones cu LEFT JOIN descuentos d ON cu.descuento_id = d.id";
            $stmt = $pdo->query($sql);
            $cupones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Cupones</h2>
            <a href="crud_cupones.php?action=create" class="btn btn-success mb-3">Agregar Cupón</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descuento</th>
                    <th>Usado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cupones as $cupon) { ?>
                    <tr>
                        <td><?php echo $cupon['id']; ?></td>
                        <td><?php echo $cupon['codigo']; ?></td>
                        <td><?php echo $cupon['nombre_descuento'] ?: 'Sin descuento'; ?></td>
                        <td><?php echo $cupon['usado'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <a href="crud_cupones.php?action=update&id=<?php echo $cupon['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_cupones.php?action=delete&id=<?php echo $cupon['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar cupón
            $id = $_GET['id'];
            $sql = "SELECT * FROM cupones WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $cupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $codigo = $_POST['codigo'];
                $descuento_id = $_POST['descuento_id'];
                $usado = isset($_POST['usado']) ? 1 : 0;

                if (empty($codigo) || empty($descuento_id)) {
                    echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
                } else {
                    try {
                        $sql = "UPDATE cupones SET codigo = ?, descuento_id = ?, usado = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$codigo, $descuento_id, $usado, $id])) {
                            echo '<div class="alert alert-success">Cupón actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el cupón.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Cupón</h2>
            <form method="POST" action="crud_cupones.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" value="<?php echo $cupon['codigo']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descuento_id" class="form-label">Descuento</label>
                    <select name="descuento_id" class="form-select" required>
                        <?php
                        // Obtener descuentos para el select
                        $sql = "SELECT id, nombre_descuento FROM descuentos";
                        $stmt = $pdo->query($sql);
                        $descuentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($descuentos as $descuento) {
                            $selected = $descuento['id'] == $cupon['descuento_id'] ? 'selected' : '';
                            echo "<option value=\"{$descuento['id']}\" $selected>{$descuento['nombre_descuento']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="usado" class="form-check-input" <?php echo $cupon['usado'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="usado">Usado</label>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Cupón</button>
            </form>
            <br>
            <a href="crud_cupones.php" class="btn btn-secondary">Ver cupones</a>
            <?php
            break;

        case 'delete':
            // Eliminar cupón
            $id = $_GET['id'];
            $sql = "DELETE FROM cupones WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo '<div class="alert alert-success">Cupón eliminado exitosamente.</div>';
            } else {
                echo '<div class="alert alert-danger">Error al eliminar el cupón.</div>';
            }
            echo '<a href="crud_cupones.php" class="btn btn-secondary">Volver a la lista</a>';
            break;

        default:
            // Acción no válida
            echo '<div class="alert alert-danger">Acción no válida.</div>';
            break;
    }
    ?>
</div>

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
