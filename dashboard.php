<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard AFFIJO2025</title>
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }

        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 55px;
            background-color: rgb(1, 62, 112);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1001;
        }

        .toolbar .menu-icon {
            font-size: 25px;
            cursor: pointer;
        }

        .toolbar .logo-container {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .toolbar .logo-container img {
            height: 35px;
        }

        .logout-button {
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
        }

        .logout-button:hover {
            color: #ccc;
        }

        .drawer {
            position: fixed;
            top: 0px;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            padding-top: 56px;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        .drawer.open {
            left: 0;
        }

        .drawer a {
            padding: 14px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .drawer a:hover {
            background-color: #f1f1f1;
        }

        .drawer a i {
            width: 25px; 
            text-align: center;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%; 
            height: 100%;
            background-color: rgba(0,0,0,0.3);
            display: none;
            z-index: 999;
        }

        .overlay.show {
            display: block;
        }

        .content {
            padding: 72px 16px 16px 16px;
        }
    </style>
</head>

<body>

    <!-- Toolbar -->
    <div class="toolbar">
        <div class="menu-icon" onclick="toggleDrawer()">☰</div>
        
        <div class="logo-container">
            <img src="JMAS blanco.png" class="logo-img" alt="Logo de AFFIJO2025">
        </div>

        <button class="logout-button" onclick="cerrarSesion()" title="Cerrar sesión">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>

    <!-- Drawer  menú-->
    <div class="drawer" id="drawer">
        <a href="dashboard.php" onclick="closeDrawer()"><i class="fas fa-home"></i> Inicio
        <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-truck-loading"></i>Salida de material</a>
        <a href="devolucion_material.php" onclick="closeDrawer()"><i class="fas fa-truck fa-flip-horizontal"></i>Devolución de material</a>
        <a href="generar_reporte.php" onclick="closeDrawer()"><i class="fas fa-file-alt"></i>Generar reporte</a>
        <a href="editar_reporte.php"><i class="fas fa-edit"></i>Editar reporte</a>
        <a href="eliminar_reporte.php"><i class="fas fa-trash"></i>Eliminar reporte</a>
        <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-arrow-alt-circle-right"></i>Mis salidas de material</a>
        <a href="#" onclick="closeDrawer()"><i class="fas fa-user"></i>Mi información</a>
        <a href="#" onclick="cerrarSesion(); closeDrawer();"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
    </div>

    <div class="overlay" id="overlay" onclick="toggleDrawer()"></div>

    <div class="content">
        <h1>Bienvenido al Panel Principal</h1>
        <p>Has iniciado sesión correctamente.</p>
    </div>

    <script>
        function toggleDrawer() {
            const drawer = document.getElementById("drawer");
            const overlay = document.getElementById("overlay");
            drawer.classList.toggle("open");
            overlay.classList.toggle("show");
        }

        function closeDrawer() {
            document.getElementById("drawer").classList.remove("open");
            document.getElementById("overlay").classList.remove("show");
        }

        function cerrarSesion() {
            if (confirm("¿Estás seguro de que quieres cerrar sesión?")) {
                alert("Cerrando sesión...");
                window.location.href = "login.php";
            }
        }
    </script>

</body>
</html>
