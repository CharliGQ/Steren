<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEREN</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">STEREN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <!-- Dropdown Usuarios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Usuarios
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_usuarios.php">CRUD Usuarios</a></li>
                            <li><a class="dropdown-item" href="crud_roles.php">CRUD Roles</a></li>
                            <li><a class="dropdown-item" href="crud_actividad_usuarios.php">CRUD Actividad Usuarios</a></li>
                            <li><a class="dropdown-item" href="crud_administradores.php">CRUD Administradores</a></li>
                            <li><a class="dropdown-item" href="crud_tipos_usuario.php">CRUD Tipos de Usuario</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Pedidos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pedidos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_pedidos.php">CRUD Pedidos</a></li>
                            <li><a class="dropdown-item" href="crud_carrito_compras.php">CRUD Carrito de Compras</a></li>
                            <li><a class="dropdown-item" href="crud_items_pedido.php">CRUD Ítems Pedido</a></li>
                            <li><a class="dropdown-item" href="crud_historial_compras.php">CRUD Historial Compras</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Productos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Productos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_productos.php">CRUD Productos</a></li>
                            <li><a class="dropdown-item" href="crud_categorias.php">CRUD Categorías</a></li>
                            <li><a class="dropdown-item" href="crud_comentarios_productos.php">CRUD Comentarios Productos</a></li>
                            <li><a class="dropdown-item" href="crud_lista_deseos.php">CRUD Lista de Deseos</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Pagos y Envíos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pagos y Envíos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_pagos.php">CRUD Pagos</a></li>
                            <li><a class="dropdown-item" href="crud_metodos_pago.php">CRUD Métodos de Pago</a></li>
                            <li><a class="dropdown-item" href="crud_envios.php">CRUD Envíos</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Inventario y Descuentos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Inventario y Descuentos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_historial_inventario.php">CRUD Historial Inventario</a></li>
                            <li><a class="dropdown-item" href="crud_descuentos.php">CRUD Descuentos</a></li>
                            <li><a class="dropdown-item" href="crud_cupones.php">CRUD Cupones</a></li>
                        </ul>
                    </li>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
