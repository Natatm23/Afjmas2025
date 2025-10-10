<?php
// Conexión a SQL Server
$serverName = "10.10.1.144";
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

$departamentos = [];
if ($conn) {
    $query = "SELECT NombreDepartamento FROM DeptoPHP";
    $stmt = sqlsrv_query($conn, $query);
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $departamentos[] = $row['NombreDepartamento'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reporte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }

        .toolbar { padding: 0 16px; background: #013E70; color: white; height: 55px; display: flex; align-items: center; justify-content: space-between; position: fixed; width: 100%; top: 0; z-index: 1001; }
        .menu-icon { font-size: 25px; cursor: pointer; }
        .logo-container { position: absolute; left: 50%; transform: translateX(-50%); }
        .logo-container img { height: 35px; }
        .logout-button { background: none; border: none; color: white; font-size: 22px; cursor: pointer; }
        .drawer { position: fixed; top: 0; left: -250px; width: 250px; height: 100%; background: #fff; padding-top: 56px; transition: left 0.3s; box-shadow: 2px 0 5px rgba(0,0,0,0.2); z-index: 1000; }
        .drawer.open { left: 0; }
        .drawer a { padding: 14px 20px; text-decoration: none; color: #333; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #eee; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); display: none; z-index: 999; }
        .overlay.show { display: block; }
        .content { padding: 72px 16px 16px 16px; }

        h1 { font-size: 22px; text-align: center; color: #014070; margin-bottom: 20px; }

        .campo {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .campo label {
            font-size: 14px;
            margin-bottom: 4px;
            color: #014070;
            font-weight: bold;
        }

        .campo input, .campo textarea, .campo select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            background-color: #fff;
        }

        input[type="file"] {
            padding: 8px;
            border-radius: 8px;
            background: #f1f1f1;
        }

        .boton-generar {
            margin-top: 20px;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Toolbar -->
<div class="toolbar">
    <div class="menu-icon" onclick="toggleDrawer()">☰</div>
    <div class="logo-container">
        <img src="JMAS blanco.png" alt="Logo">
    </div>
    <button class="logout-button" onclick="cerrarSesion()" title="Cerrar sesión">
        <i class="fas fa-sign-out-alt"></i>
    </button>
</div>

<!-- Drawer -->
<<div class="drawer" id="drawer">
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


<!-- Contenido -->
<div class="content">
    <h1>✏️ Editar Reporte</h1>
    <!-- FORMULARIO PARA BUSCAR -->
    <form action="editar_reporte.php" method="POST" style="margin-bottom: 20px;">
        <div class="campo">
            <label for="buscar_ticket">Buscar por ID Ticket</label>
            <div style="display: flex; gap: 10px;">
                <input type="number" id="buscar_ticket" name="buscar_ticket" placeholder="Ingrese ID Ticket" required>
                <button type="submit" style="background-color: #007bff; color: white; border: none; padding: 10px 16px; border-radius: 10px; cursor: pointer;">
                    🔍 Buscar
                </button>
            </div>
        </div>
    </form>

    <!-- FORMULARIO PARA EDITAR -->
    <form action="guardar_edicion.php" method="POST" enctype="multipart/form-data">

        <!-- DEPARTAMENTO -->
        <div class="campo">
            <label for="departamento">Departamento</label>
            <select id="departamento" name="departamento" required>
                <option value="">Selecciona una opción</option>
                <?php foreach ($departamentos as $dep): ?>
                    <option value="<?= htmlspecialchars($dep) ?>"><?= htmlspecialchars($dep) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- ID TICKET -->
        <div class="campo">
            <label for="id_ticket">ID Ticket</label>
            <input type="number" id="id_ticket" name="id_ticket" required>
        </div>

        <!-- FECHAS -->
        <div class="campo">
            <label for="fecha_asignacion">Fecha de asignación</label>
            <input type="date" id="fecha_asignacion" name="fecha_asignacion" required>
        </div>

        <div class="campo">
            <label for="fecha_reparacion">Fecha de reparación</label>
            <input type="date" id="fecha_reparacion" name="fecha_reparacion" required>
        </div>

        <!-- COLONIA Y DIRECCION -->
        <div class="campo">
            <label for="colonia">Colonia</label>
            <input type="text" id="colonia" name="colonia" required>
        </div>

        <div class="campo">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" required>
        </div>

        <!-- TIPO DE SUELO -->
        <div class="campo">
            <label for="tipo_suelo">Tipo de suelo</label>
            <select id="tipo_suelo" name="tipo_suelo" required>
                <option value="">Selecciona una opción</option>
                <option value="Terracería">TERRACERIA</option>
                <option value="Concreto">CONCRETO</option>
                <option value="Pavimento">PAVIMENTO</option>
            </select>
        </div>

        <!-- REPORTANTE Y TELEFONO -->
        <div class="campo">
            <label for="reportante">Reportante</label>
            <input type="text" id="reportante" name="reportante" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono del reportante</label>
            <input type="tel" id="telefono" name="telefono" pattern="[0-9]{10}" maxlength="10" required>
        </div>

        <!-- TECNICO Y N° SOLICITUD -->
        <div class="campo">
            <label for="tecnico">Técnico</label>
            <input type="text" id="tecnico" name="tecnico" required>
        </div>

        <div class="campo">
            <label for="solicitud_materiales">Número de solicitud de materiales</label>
            <input type="number" id="solicitud_materiales" name="solicitud_materiales" required>
        </div>

        <!-- FOTOS -->
        <div class="campo">
            <label>Foto 1</label>
            <input type="file" name="foto1" accept="image/*">
        </div>
        <div class="campo">
            <label>Foto 2</label>
            <input type="file" name="foto2" accept="image/*">
        </div>
        <div class="campo">
            <label>Foto 3</label>
            <input type="file" name="foto3" accept="image/*">
        </div>
        <div class="campo">
            <label>Foto 4</label>
            <input type="file" name="foto4" accept="image/*">
        </div>

        <button type="submit" class="boton-generar">💾 Guardar Cambios</button>
    </form>

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
</div>
