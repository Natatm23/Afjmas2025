<?php
require "conexion_lecturacel.php";

$direccion = "";
$colonia = "";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $modo   = $_POST['modo_busqueda'] ?? '';
    $id     = $_POST['id_medidor'] ?? '';
    $nombre = $_POST['nombre_cliente'] ?? '';

    if ($modo == "id" && !empty($id)) {

        $sql = "SELECT mednume_us, dire_us, colo_us 
                FROM usuarios 
                WHERE mednume_us = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

    } elseif ($modo == "nombre" && !empty($nombre)) {

        $sql = "SELECT mednume_us, dire_us, colo_us 
                FROM usuarios 
                WHERE nomb_us LIKE ?";

        $like = "%".$nombre."%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $like);

    } else {
        $mensaje = "⚠ Debes ingresar un valor para buscar.";
    }

    if (isset($stmt)) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $mensaje = "❌ No se encontró ningún medidor.";
        } else {
            $data = $result->fetch_assoc();
            $direccion = $data['dire_us'];
            $colonia   = $data['colo_us'];
        }
    }
}
?>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Buscar Medidor</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background:#f9f9f9; overflow-x: hidden; }

/* Toolbar */
.toolbar {
    position: fixed; top: 0; left: 0; right: 0;
    height: 55px;
    background: rgb(1,62,112);
    color: #fff;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding: 0 16px;
    box-shadow:0 2px 4px rgba(0,0,0,0.2);
    z-index: 1002;
}
.menu-icon { font-size:24px; cursor:pointer; user-select:none; }
.logo-container { position:absolute; left:50%; transform:translateX(-50%); }
.logo-container img { height:35px; display:block; }
.logout-button { background:none; border:none; color:white; font-size:20px; cursor:pointer; }

/* Drawer */
.drawer {
    position: fixed;
    top: 0; left: -260px;
    width: 260px; height: 100%;
    background:#fff;
    box-shadow:2px 0 8px rgba(0,0,0,0.12);
    padding-top:56px;
    transition: left .28s ease;
    z-index: 1001;
}
.drawer.open { left: 0; }
.drawer a {
    display:flex; align-items:center; gap:12px;
    padding:14px 18px; color:#333; text-decoration:none; border-bottom:1px solid #eee;
}
.drawer a:hover { background:#f5f5f5; }

/* Overlay */
.overlay {
    position: fixed; inset:0;
    background: rgba(0,0,0,0.32);
    display: none;
    z-index: 1000;
}
.overlay.show { display:block; }

/* ===== FORMULARIO MODERNO ===== */
.form-container.moderno {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: auto;
    border: 1px solid #e0e0e0;
}

.titulo-form {
    text-align: center;
    margin-bottom: 15px;
    font-size: 22px;
    color: #2b4c7e;
    font-weight: 700;
}

.subtitulo {
    font-weight: bold;
    color: #333;
}

/* CAMPOS */
.campo {
    margin-bottom: 15px;
}

.campo label {
    font-weight: 600;
    color: #444;
    margin-bottom: 3px;
    display: block;
}

.campo input, 
.campo textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #d0d0d0;
    border-radius: 8px;
    font-size: 15px;
    transition: 0.2s;
}

.campo input:focus,
.campo textarea:focus {
    border-color: #2b4c7e;
    box-shadow: 0 0 5px rgba(43,76,126,0.3);
}

/* RADIO buttons */
.radio-group {
    display: flex;
    align-items: center;       /* Alinea radio y texto al mismo nivel */
    gap: 25px;                 /* Espacio entre cada opción */
    margin-top: 8px;
}

.radio-group label {
    display: flex;
    align-items: center;       /* Mantiene radio + texto alineados */
    gap: 6px;                  /* Espacio entre radio y texto */
    font-size: 15px;
    cursor: pointer;
}


/* BOTÓN moderno */
.moderno-btn {
    width: 100%;
    background: #2b4c7e;
    color: white;
    padding: 12px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 8px;
    margin-top: 5px;
    transition: 0.2s;
}

.moderno-btn:hover {
    background: #1e3558;
}

/* SEPARADOR bonito */
.separador {
    border: none;
    border-top: 1px solid #ccc;
    margin: 25px 0;
}

/* TABLA modernizada */
.moderno-tabla {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    border-radius: 10px;
    overflow: hidden;
}

.moderno-tabla thead {
    background: #2b4c7e;
    color: white;
}

.moderno-tabla th,
.moderno-tabla td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.moderno-tabla tr:nth-child(even) {
    background: #f7f7f7;
}

/* TEXTAREA Observaciones */
.moderno-textarea {
    height: 100px;
}

/* RESPONSIVO */
@media (max-width: 600px) {
    .moderno-tabla th,
    .moderno-tabla td {
        font-size: 12px;
        padding: 6px;
    }
}
</style>
</head>

<body>

<!-- Toolbar -->
<div class="toolbar">
    <div class="menu-icon" id="menuToggle" aria-label="Abrir menú">☰</div>
    <div class="logo-container">
        <img src="JMAS blanco.png" alt="Logo AFFIJO2025">
    </div>
    <button class="logout-button" onclick="cerrarSesion()" title="Cerrar sesión">
        <i class="fas fa-sign-out-alt"></i>
    </button>
</div>

<!-- Drawer -->
<nav id="drawer" class="drawer" aria-hidden="true">
    <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
    <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
    <a href="salida_material.php"><i class="fas fa-truck-loading"></i> Salida de material</a>
    <a href="devolucion_material.php"><i class="fas fa-truck fa-flip-horizontal"></i> Devolución de material</a>
    <a href="generar_reporte.php"><i class="fas fa-file-alt"></i> Generar reporte</a>
    <a href="editar_reporte.php"><i class="fas fa-edit"></i> Editar reporte</a>
    <a href="eliminar_reporte.php"><i class="fas fa-trash"></i> Eliminar reporte</a>
    <a href="stock.php"><i class="fas fa-boxes"></i> Stock</a>
    <a href="#"><i class="fas fa-user"></i> Mi información</a>
    <a href="#" onclick="cerrarSesion(); return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
</nav>

<!-- Overlay -->
<div id="overlay" class="overlay" role="button" aria-label="Cerrar menú"></div>

<!-- === FORMULARIO MODERNO === -->
<div class="form-container moderno">
    <h2 class="titulo-form">🔍 Buscar Medidor</h2>
    
   <form method="POST" onsubmit="buscarMedidor(event)">

    <!-- Tipo de búsqueda -->
    <div class="campo">
        <label class="subtitulo">Buscar por:</label>

        <div class="radio-group">
            <label><input type="radio" name="modo_busqueda" value="id" checked onclick="mostrarCampo('id')"> Numero de medidor</label>
            <label><input type="radio" name="modo_busqueda" value="nombre" onclick="mostrarCampo('nombre')"> Nombre</label>
            <label><input type="radio" name="modo_busqueda" value="id cuenta" onclick="mostrarCampo('id cuenta')"> Id cuenta</label>
            <label><input type="radio" name="modo_busqueda" value="cuenta" onclick="mostrarCampo('cuenta')"> Cuenta</label>
        </div>
    </div>

    <!-- Campo para Numero de medidor-->
    <div class="campo" id="campo_id">
        <label for="id_medidor">Numero de Medidor</label>
        <input type="number" id="id_medidor" name="id_medidor" placeholder="Ingresa el numero de medidor">
    </div>

    <!-- Campo para Nombre -->
    <div class="campo" id="campo_nombre" style="display:none;">
        <label for="nombre_cliente">Nombre del Usuario</label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Ingresa el nombre">
    </div>

    <!-- Campo para id cuenta -->
    <div class="campo" id="campo_id_cuenta" style="display:none;">
        <label for="id_cuenta">Id cuenta</label>
        <input type="text" id="id_cuenta" name="id_cuenta" placeholder="Ingresa el Id de la cuenta">
    </div>

    <!-- Campo para cuenta -->
    <div class="campo" id="campo_cuenta" style="display:none;">
        <label for="cuenta">Cuenta</label>
        <input type="text" id="cuenta" name="cuenta" placeholder="Ingresa la cuenta">
    </div>


    <button type="submit" class="btn moderno-btn">Buscar</button>

    <!-- AQUI VA EL MENSAJE, VACÍO -->
    <p id="mensaje" style="color:red; font-weight:bold; margin-top:10px;"></p>

    <hr class="separador">

    <div class="campo">
        <label>Nombre</label>
        <input type="text" id="Nombre" name="Nombre" readonly>
    </div>

    <div class="campo">
        <label>Dirección</label>
        <textarea id="direccion" class="direccion-area moderno-textarea" readonly></textarea>
    </div>

    <div class="campo">
        <label>Colonia</label>
        <input type="text" id="colonia" name="colonia" readonly>
    </div>

        <!-- TABLA -->
        <table class="tabla-lecturas moderno-tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Lectura anterior</th>
                    <th>Lectura actual</th>
                    <th>Consumo</th>
                    <th>Lectura real</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="f1_fecha"></td>
                    <td id="f1_ant"></td>
                    <td id="f1_act"></td>
                    <td id="f1_con"></td>
                    <td id="f1_real"></td>
                </tr>
                <tr>
                    <td id="f2_fecha"></td>
                    <td id="f2_ant"></td>
                    <td id="f2_act"></td>
                    <td id="f2_con"></td>
                    <td id="f2_real"></td>
                </tr>
                <tr>
                    <td id="f3_fecha"></td>
                    <td id="f3_ant"></td>
                    <td id="f3_act"></td>
                    <td id="f3_con"></td>
                    <td id="f3_real"></td>
                </tr>
                <tr>
                    <td id="f4_fecha"></td>
                    <td id="f4_ant"></td>
                    <td id="f4_act"></td>
                    <td id="f4_con"></td>
                    <td id="f4_real"></td>
                </tr>
            </tbody>
        </table>

        <div class="campo">
            <label>Observaciones</label>
            <textarea id="observaciones" class="observaciones-area moderno-textarea" readonly></textarea>
        </div>

    </form>
</div>


<script>
function mostrarCampo(modo) {
    // Ocultar todos
    document.getElementById("campo_id").style.display = "none";
    document.getElementById("campo_nombre").style.display = "none";
    document.getElementById("campo_id_cuenta").style.display = "none";
    document.getElementById("campo_cuenta").style.display = "none";

    // Mostrar el correspondiente
    if (modo === "id") {
        document.getElementById("campo_id").style.display = "block";
    }
    if (modo === "nombre") {
        document.getElementById("campo_nombre").style.display = "block";
    }
    if (modo === "id cuenta") {
        document.getElementById("campo_id_cuenta").style.display = "block";
    }
    if (modo === "cuenta") {
        document.getElementById("campo_cuenta").style.display = "block";
    }
}
</script>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');

    if(!drawer || !overlay || !menuToggle) return;

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('show');
        drawer.setAttribute('aria-hidden','false');
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('show');
        drawer.setAttribute('aria-hidden','true');
    }

    menuToggle.addEventListener('click', function(){
        if(drawer.classList.contains('open')) closeDrawer();
        else openDrawer();
    });

    overlay.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') closeDrawer();
    });
});

function cerrarSesion(){
    if(confirm('¿Estás seguro de que quieres cerrar sesión?')){
        window.location.href='login.php';
    }
}
</script>

<script>
function buscarMedidor(event) {
    event.preventDefault();

    const formData = new FormData(document.querySelector("form"));

    fetch("buscar_medidor.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(data => {

        // Mostrar mensaje
        document.getElementById("mensaje").innerText = data.mensaje ?? "";

        // Rellenar datos
        document.getElementById("direccion").value = data.direccion ?? "";
        document.getElementById("colonia").value   = data.colonia ?? "";
    });
}
</script>
</body>
</html>
