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
    <title>CRUD Métodos de Pago</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear método de pago
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_metodo = $_POST['nombre_metodo'];

                // Validación básica
                if (empty($nombre_metodo)) {
                    echo '<div class="alert alert-danger">El nombre del método de pago es obligatorio.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO metodos_pago (nombre_metodo) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_metodo])) {
                            echo '<div class="alert alert-success">Método de pago agregado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al agregar el método de pago.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Agregar Método de Pago</h2>
            <form method="POST" action="crud_metodos_pago.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_metodo" class="form-label">Nombre del Método</label>
                    <input type="text" name="nombre_metodo" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Método</button>
            </form>
            <br>
            <a href="crud_metodos_pago.php" class="btn btn-secondary">Ver métodos de pago</a>
            <?php
            break;

        case 'read':
            // Leer métodos de pago
            $sql = "SELECT * FROM metodos_pago";
            $stmt = $pdo->query($sql);
            $metodos_pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Métodos de Pago</h2>
            <a href="crud_metodos_pago.php?action=create" class="btn btn-success mb-3">Agregar Método</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Método</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($metodos_pago as $metodo) { ?>
                    <tr>
                        <td><?php echo $metodo['id']; ?></td>
                        <td><?php echo $metodo['nombre_metodo']; ?></td>
                        <td>
                            <a href="crud_metodos_pago.php?action=update&id=<?php echo $metodo['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_metodos_pago.php?action=delete&id=<?php echo $metodo['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar método de pago
            $id = $_GET['id'];
            $sql = "SELECT * FROM metodos_pago WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $metodo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre_metodo = $_POST['nombre_metodo'];

                if (empty($nombre_metodo)) {
                    echo '<div class="alert alert-danger">El nombre del método de pago es obligatorio.</div>';
                } else {
                    try {
                        $sql = "UPDATE metodos_pago SET nombre_metodo = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre_metodo, $id])) {
                            echo '<div class="alert alert-success">Método de pago actualizado exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar el método de pago.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Método de Pago</h2>
            <form method="POST" action="crud_metodos_pago.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre_metodo" class="form-label">Nombre del Método</label>
                    <input type="text" name="nombre_metodo" class="form-control" value="<?php echo $metodo['nombre_metodo']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Método</button>
            </form>
            <br>
            <a href="crud_metodos_pago.php" class="btn btn-secondary">Ver métodos de pago</a>
            <?php
            break;

        case 'delete':
            // Eliminar método de pago
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM metodos_pago WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_metodos_pago.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar el método de pago.</div>';
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
