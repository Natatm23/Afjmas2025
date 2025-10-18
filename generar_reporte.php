<?php
//Iniciar sesión y recuperar datos del usuario logueado
session_start();

// Si no hay sesión, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Guardar variables desde la sesión
$usuario = $_SESSION['usuario'];
$id_empleado = $_SESSION['id_empleado'];
$nombre_departamento = $_SESSION['nombre_departamento'];

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
    //conexion para cargar los departamentos en el combo
    $query = "SELECT NombreDepartamento FROM DeptoPHP";
    $stmt = sqlsrv_query($conn, $query);
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $departamentos[] = $row['NombreDepartamento'];
    }

    //Traer el último número de solicitud
    $sql = "SELECT MAX(IdNumeroSolicitud) AS ultimo FROM AFM_Salida_Material"; 
    $stmt2 = sqlsrv_query($conn, $sql);
    if ($stmt2 && $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
        $nuevoConsecutivo = $row2['ultimo'] + 1;
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte</title>
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

        .boton-agregar {
            background-color: #014070;
            color: white;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(1, 64, 112, 0.2);
        }

        .boton-agregar:hover {
            background-color: #012d50;
        }

        table {
            width: 100%;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            font-size: 13px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        td button {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        td button:hover {
            background-color: darkred;
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
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
        }

        .boton-generar:hover {
            background-color: #218838;
        }

        @media (max-width: 600px) {
            h1, h2 {
                font-size: 18px;
            }

            .boton-agregar,
            .boton-generar {
                font-size: 15px;
            }

            table {
                font-size: 12px;
            }
        }

        .fotos-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.foto-item {
    display: flex;
    flex-direction: column;
    width: 48%;
    margin-bottom: 15px;
}

.preview-wrapper {
    position: relative;
    margin-top: 5px;
    width: 100%;
}

.preview-img {
    display: none;
    width: 100%;
    max-height: 150px;
    object-fit: cover;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.btn-quitar {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255,0,0,0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-weight: bold;
    z-index: 2;
}

@media (max-width: 600px){
    .foto-item { width: 100%; }
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
<div class="drawer" id="drawer">
        <a href="dashboard.php" onclick="closeDrawer()"><i class="fas fa-home"></i> Inicio
        <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-truck-loading"></i>Salida de material</a>
        <a href="devolucion_material.php" onclick="closeDrawer()"><i class="fas fa-truck fa-flip-horizontal"></i>Devolución de material</a>
        <a href="generar_reporte.php" onclick="closeDrawer()"><i class="fas fa-file-alt"></i>Generar reporte</a>
        <a href="editar_reporte.php"><i class="fas fa-edit"></i>Editar reporte</a>
        <a href="eliminar_reporte.php"><i class="fas fa-trash"></i>Eliminar reporte</a>
        <a href="#" onclick="closeDrawer()"><i class="fas fa-boxes"></i> Stock</a>
        <a href="#" onclick="closeDrawer()"><i class="fas fa-user"></i>Mi información</a>
        <a href="#" onclick="cerrarSesion(); closeDrawer();"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
    </div>


<!-- Contenido -->
<div class="content">
    <h1>📄 Generar Reporte</h1>
    <form action="guardar_reporte.php" method="POST" enctype="multipart/form-data">
        
        <!-- DEPARTAMENTO -->
        <div class="campo">
            <label for="departamento">Departamento</label>
            <select id="departamento" name="departamento" disabled>
                <option value="">Seleccione un departamento</option>
                <?php include("obtener_departamentos.php"); ?>
            </select>

        <div class="campo">
            <label for="usuario">Técnico</label>
            <input type="text" id="usuario" name="usuario" 
            value="<?php echo htmlspecialchars($usuario); ?>" readonly>
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
   
        <div class="campo">
            <label for="Observacion">Observaciones</label>
            <textarea id="Observaciones" name="Observaciones" rows="5" required></textarea>
        </div>

        <h1><i class="fas fa-tools"></i> Materiales utilizados</h1>

       <div class="campo">
            <label for="material">Codigo del material</label>
            <input type="text" id="material" required>
        </div>

        <div class="campo">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" required min="1" step="1">
        </div>

        

        <button type="submit" class="boton-agregar">➕ Agregar Material</button>
    </form>

    <h2 style="margin-top: 30px; font-size: 18px; color: #014070;">📦 Materiales Agregados</h2>

    <div style="overflow-x: auto; margin-top: 10px;">
        <table id="tablaMateriales">
    <thead style="background-color: #014070; color: white;">
        <tr>
            <th>Departamento</th>
            <th>Técnico</th>
             <th>Id ticket</th>  
            <th>Código material</th>
            <th>Cantidad</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <!-- Filas agregadas dinámicamente -->
    </tbody>
        </table>
    </div>
</div>
<!-- FOTOS -->
<div class="fotos-container">
  <div class="foto-item">
    <label>Foto Medidor anterior:</label>
    <input type="file" name="foto1" accept="image/*" id="foto1" onchange="mostrarPreview('foto1','preview1')">
    <div class="preview-wrapper">
      <button type="button" class="btn-quitar" onclick="limpiarFoto('foto1','preview1')">❌</button>
      <img id="preview1" src="" alt="Preview" class="preview-img">
    </div>
  </div>

  <div class="foto-item">
    <label>Foto Domicilio:</label>
    <input type="file" name="foto2" accept="image/*" id="foto2" onchange="mostrarPreview('foto2','preview2')">
    <div class="preview-wrapper">
      <button type="button" class="btn-quitar" onclick="limpiarFoto('foto2','preview2')">❌</button>
      <img id="preview2" src="" alt="Preview" class="preview-img">
    </div>
  </div>

  <div class="foto-item">
    <label>Foto Medidor nuevo instalado:</label>
    <input type="file" name="foto3" accept="image/*" id="foto3" onchange="mostrarPreview('foto3','preview3')">
    <div class="preview-wrapper">
      <button type="button" class="btn-quitar" onclick="limpiarFoto('foto3','preview3')">❌</button>
      <img id="preview3" src="" alt="Preview" class="preview-img">
    </div>
  </div>

  <div class="foto-item">
    <label>Foto Notificación:</label>
    <input type="file" name="foto4" accept="image/*" id="foto4" onchange="mostrarPreview('foto4','preview4')">
    <div class="preview-wrapper">
      <button type="button" class="btn-quitar" onclick="limpiarFoto('foto4','preview4')">❌</button>
      <img id="preview4" src="" alt="Preview" class="preview-img">
    </div>
  </div>
  
    <button class="boton-generar" onclick="generarOrden()">🧾 Generar Reporte</button>
</div>


<script>
// Funciones globales para toolbar y drawer
function toggleDrawer() {
    const drawer = document.getElementById("drawer");
    drawer.classList.toggle("open");
}

function closeDrawer() {
    const drawer = document.getElementById("drawer");
    drawer.classList.remove("open");
}

function cerrarSesion() {
    alert("Sesión cerrada correctamente");
    window.location.href = "login.php";
}

document.addEventListener("DOMContentLoaded", function() {
    const botonAgregar = document.querySelector(".boton-agregar");
    botonAgregar.addEventListener("click", function(event) {
        event.preventDefault();
        agregarMaterial();
    });

    function agregarMaterial() {
        const departamento = document.getElementById("departamento").value.trim();
        const tecnico = document.getElementById("usuario").value.trim();
        const idTicket = document.getElementById("id_ticket").value.trim();
        const material = document.getElementById("material").value.trim();
        const cantidad = document.getElementById("cantidad").value.trim();

        if (!departamento || !tecnico || !idTicket || !material || !cantidad) {
            alert("Por favor completa todos los campos antes de agregar el material.");
            return;
        }

        const tabla = document.getElementById("tablaMateriales").getElementsByTagName('tbody')[0];
        const fila = tabla.insertRow();

        fila.insertCell(0).textContent = departamento;
        fila.insertCell(1).textContent = tecnico;
        fila.insertCell(2).textContent = idTicket;
        fila.insertCell(3).textContent = material;
        fila.insertCell(4).textContent = cantidad;

        const celdaAccion = fila.insertCell(5);
        const botonEliminar = document.createElement("button");
        botonEliminar.innerText = "❌";
        botonEliminar.type = "button";
        botonEliminar.className = "boton-eliminar";
        botonEliminar.onclick = function () {
            tabla.deleteRow(fila.rowIndex - 1);
        };
        celdaAccion.appendChild(botonEliminar);

        document.getElementById("material").value = "";
        document.getElementById("cantidad").value = "";
        document.getElementById("material").focus();
    }
});

// Funciones globales para fotos
function mostrarPreview(inputId, imgId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(imgId);

    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function limpiarFoto(inputId, imgId){
    const input = document.getElementById(inputId);
    const preview = document.getElementById(imgId);

    input.value = '';
    preview.src = '';
    preview.style.display = 'none';
}
</script>
</body>
</html>
