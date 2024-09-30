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
    <title>CRUD Categorías</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php
    // Manejo de acciones
    switch ($action) {
        case 'create':
            // Crear categoría
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];

                // Validación básica
                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre de la categoría es obligatorio.</div>';
                } else {
                    try {
                        $sql = "INSERT INTO categorias (nombre) VALUES (?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre])) {
                            echo '<div class="alert alert-success">Categoría creada exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al crear la categoría.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Crear Categoría</h2>
            <form method="POST" action="crud_categorias.php?action=create" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Categoría</button>
            </form>
            <br>
            <a href="crud_categorias.php" class="btn btn-secondary">Ver categorías</a>
            <?php
            break;

        case 'read':
            // Leer categorías
            $sql = "SELECT * FROM categorias";
            $stmt = $pdo->query($sql);
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mb-4">Lista de Categorías</h2>
            <a href="crud_categorias.php?action=create" class="btn btn-success mb-3">Crear Categoría</a>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categorias as $categoria) { ?>
                    <tr>
                        <td><?php echo $categoria['id']; ?></td>
                        <td><?php echo $categoria['nombre']; ?></td>
                        <td>
                            <a href="crud_categorias.php?action=update&id=<?php echo $categoria['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="crud_categorias.php?action=delete&id=<?php echo $categoria['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            break;

        case 'update':
            // Actualizar categoría
            $id = $_GET['id'];
            $sql = "SELECT * FROM categorias WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];

                if (empty($nombre)) {
                    echo '<div class="alert alert-danger">El nombre de la categoría es obligatorio.</div>';
                } else {
                    try {
                        $sql = "UPDATE categorias SET nombre = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$nombre, $id])) {
                            echo '<div class="alert alert-success">Categoría actualizada exitosamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar la categoría.</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
            ?>
            <h2 class="mb-4">Editar Categoría</h2>
            <form method="POST" action="crud_categorias.php?action=update&id=<?php echo $id; ?>" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" name="nombre" value="<?php echo $categoria['nombre']; ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
            </form>
            <br>
            <a href="crud_categorias.php" class="btn btn-secondary">Ver categorías</a>
            <?php
            break;

        case 'delete':
            // Eliminar categoría
            $id = $_GET['id'];
            try {
                $sql = "DELETE FROM categorias WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$id])) {
                    header("Location: crud_categorias.php?action=read");
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Error al eliminar la categoría.</div>';
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
