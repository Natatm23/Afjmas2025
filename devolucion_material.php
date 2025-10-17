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

//Conexión a SQL Server
$serverName = "10.10.1.144";
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}

// Traer el último número de solicitud
$sql = "SELECT MAX(IdNumeroDevolucion) AS ultimo FROM AFM_Devolucion_Material"; 
$stmt2 = sqlsrv_query($conn, $sql);
if ($stmt2 && $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $nuevoConsecutivo = $row2['ultimo'] + 1;
} else {
    $nuevoConsecutivo = 1; // En caso de que no haya registros
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolución de Material</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

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

        .menu-icon { font-size: 25px; cursor: pointer; }
        .logo-container { position: absolute; left: 50%; transform: translateX(-50%); }
        .logo-container img { height: 35px; }
        .logout-button { background: none; border: none; color: white; font-size: 22px; cursor: pointer; }
        .logout-button:hover { color: #ccc; }

        .drawer {
            position: fixed;
            top: 0;
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

        .drawer.open { left: 0; }
        .drawer a {
            padding: 14px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .drawer a:hover { background-color: #f1f1f1; }
        .drawer a i { width: 25px; text-align: center; }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.3);
            display: none;
            z-index: 999;
        }
        .overlay.show { display: block; }

        .content { padding: 72px 16px 16px 16px; }
        h1 { margin-bottom: 16px; }

        .campo { display: flex; flex-direction: column; }
        .campo label {
            font-size: 14px; margin-bottom: 4px;
            color: #014070; font-weight: bold;
        }

        .campo input, .campo textarea, .campo select {
            padding: 10px; border: 1px solid #ccc;
            border-radius: 10px; font-size: 15px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .boton-agregar {
            background-color: #014070; color: white;
            padding: 14px; font-size: 16px; font-weight: bold;
            border: none; border-radius: 12px; cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(1, 64, 112, 0.2);
        }
        .boton-agregar:hover { background-color: #012d50; }

        table {
            width: 100%; background-color: white;
            border-radius: 10px; overflow: hidden;
            font-size: 13px; border-collapse: collapse;
        }
        th, td {
            padding: 10px; text-align: left;
            border-bottom: 1px solid #ddd;
        }
        td button {
            background-color: red; color: white;
            border: none; padding: 6px 10px;
            border-radius: 6px; cursor: pointer;
        }
        td button:hover { background-color: darkred; }

        .boton-generar {
            margin-top: 20px; width: 100%;
            background-color: #28a745; color: white;
            padding: 14px; font-size: 16px; font-weight: bold;
            border: none; border-radius: 12px;
            cursor: pointer; transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
        }
        .boton-generar:hover { background-color: #218838; }

        @media (max-width: 600px) {
            h1, h2 { font-size: 18px; }
            .boton-agregar, .boton-generar { font-size: 15px; }
            table { font-size: 12px; }
        }
    </style>
</head>
<body>

<!-- Toolbar -->
<div class="toolbar">
    <div class="menu-icon" onclick="toggleDrawer()">☰</div>
    <div class="logo-container">
        <img src="JMAS blanco.png" class="logo-img" alt="Logo">
    </div>
    <button class="logout-button" onclick="cerrarSesion()" title="Cerrar sesión">
        <i class="fas fa-sign-out-alt"></i>
    </button>
</div>

<div class="drawer" id="drawer">
    <a href="dashboard.php" onclick="closeDrawer()"><i class="fas fa-home"></i> Inicio</a>
    <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-truck-loading"></i> Salida de material</a>
    <a href="devolucion_material.php" onclick="closeDrawer()"><i class="fas fa-truck fa-flip-horizontal"></i> Devolución de material</a>
    <a href="generar_reporte.php" onclick="closeDrawer()"><i class="fas fa-file-alt"></i> Generar reporte</a>
    <a href="editar_reporte.php"><i class="fas fa-edit"></i> Editar reporte</a>
    <a href="eliminar_reporte.php"><i class="fas fa-trash"></i> Eliminar reporte</a>
    <a href="#" onclick="closeDrawer()"><i class="fas fa-boxes"></i> Stock</a>
    <a href="#" onclick="closeDrawer()"><i class="fas fa-user"></i> Mi información</a>
    <a href="#" onclick="cerrarSesion(); closeDrawer();"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
</div>

<div class="overlay" id="overlay" onclick="toggleDrawer()"></div>

<!-- Contenido -->
<div class="content">
    <h1 style="color: #014070; font-size: 22px; text-align: center; margin-bottom: 20px;">🔄 Devolución de Material</h1>

    <form id="formDevolucionMaterial" onsubmit="agregarDevolucion(); return false;" style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Campos principales -->

        <div class="campo">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" 
           value="<?php echo htmlspecialchars($usuario); ?>" readonly>
        </div>
    

        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" required readonly>
        </div>

        <div class="campo">
            <label for="numero_devolucion">Número de Devolución</label>
            <input type="text" id="IdNumeroDevolucion" name="numeroDevolucion" value="<?php echo $nuevoConsecutivo; ?>" readonly>
        </div>

        <div class="campo">
            <label for="departamento">Departamento</label>
            <select id="departamento" name="departamento" disabled>
                <option value="">Seleccione un departamento</option>
                <?php include("obtener_departamentos.php"); ?>
            </select>
        </div>

        <div class="campo">
            <label for="material">Código del material</label>
            <input type="text" id="material" name="material" required>
        </div>

        <div class="campo">
            <label for="cantidad">Cantidad devuelta</label>
            <input type="number" id="cantidad" name="cantidad" required min="1" step="1">
        </div>

        <div class="campo">
            <label for="motivo">Motivo de devolución</label>
            <textarea id="motivo" name="motivo" rows="3" required></textarea>
        </div>

        <button type="submit" class="boton-agregar">➕ Agregar Devolución</button>

        <!-- Tabla dinámica de materiales -->
        <h2 style="margin-top: 30px; font-size: 18px; color: #014070;">📦 Materiales Devueltos</h2>
        <div style="overflow-x: auto; margin-top: 10px;">
            <table id="tablaDevoluciones">
                <thead style="background-color: #014070; color: white;">
                    <tr>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <button type="button" class="boton-generar" onclick="generarDevolucion()">🧾 Generar Registro de Devolución</button>
    </form>
</div>

<script>
window.addEventListener("DOMContentLoaded", function () {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    document.getElementById("fecha").value = `${yyyy}-${mm}-${dd}`;
});

function toggleDrawer() {
    document.getElementById("drawer").classList.toggle("open");
    document.getElementById("overlay").classList.toggle("show");
}
function closeDrawer() {
    document.getElementById("drawer").classList.remove("open");
    document.getElementById("overlay").classList.remove("show");
}
function cerrarSesion() {
    alert("Sesión cerrada correctamente");
    window.location.href = "login.php";
}

let contador = <?php echo $contadorInicial ?? 0; ?>; // Comienza desde el último

window.addEventListener("DOMContentLoaded", function () {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    document.getElementById("fecha").value = `${yyyy}-${mm}-${dd}`;
});

function toggleDrawer() {
    document.getElementById("drawer").classList.toggle("open");
    document.getElementById("overlay").classList.toggle("show");
}
function closeDrawer() {
    document.getElementById("drawer").classList.remove("open");
    document.getElementById("overlay").classList.remove("show");
}
function cerrarSesion() {
    alert("Sesión cerrada correctamente");
    window.location.href = "login.php";
}

//Funcion para agregar material a la tabla
function agregarDevolucion() {
    const material = document.getElementById("material").value.trim();
    const cantidad = document.getElementById("cantidad").value.trim();
    const motivo = document.getElementById("motivo").value.trim();

    if (!material || !cantidad || !motivo) {
        alert("Por favor completa todos los campos antes de agregar la devolución.");
        return;
    }

    contador++;
    if (contador > 10) {
        alert("Solo se permiten hasta 10 materiales por devolución.");
        return;
    }

    const tabla = document.getElementById("tablaDevoluciones").getElementsByTagName('tbody')[0];
    const fila = tabla.insertRow();

    fila.innerHTML = `
        <td><input type="hidden" name="material${contador}" value="${material}">${material}</td>
        <td><input type="hidden" name="cantidad${contador}" value="${cantidad}">${cantidad}</td>
        <td><input type="hidden" name="justificacion${contador}" value="${motivo}">${motivo}</td>
        <td><button type="button" onclick="eliminarFila(this)">❌</button></td>
    `;

    // Limpiar campos
    document.getElementById("material").value = "";
    document.getElementById("cantidad").value = "";
    document.getElementById("motivo").value = "";
    document.getElementById("material").focus();
}

function eliminarFila(btn) {
    const fila = btn.closest("tr");
    fila.remove();
    contador--;
}

function generarDevolucion() {
    const numeroDevolucion = document.getElementById("IdNumeroDevolucion").value;
    const departamento = document.getElementById("departamento").value;
    const fecha = document.getElementById("fecha").value;
    const usuario = document.getElementById("usuario").value;
    const idEmpleado = "<?php echo $_SESSION['id_empleado']; ?>"; 

    const filas = document.querySelectorAll("#tablaDevoluciones tbody tr");
    const formData = new FormData();

    formData.append("numeroDevolucion", numeroDevolucion);
    formData.append("departamento", departamento);
    formData.append("fecha", fecha);
    formData.append("usuario", usuario);
    formData.append("idEmpleado", idEmpleado); 

    filas.forEach((fila, index) => {
        if (index < 10) {
            const celdas = fila.getElementsByTagName("td");
            formData.append("material" + (index + 1), celdas[0].textContent);
            formData.append("cantidad" + (index + 1), celdas[1].textContent);
            formData.append("justificacion" + (index + 1), celdas[2].textContent);
        }
    });

    fetch("insertar_devolucion.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(err => console.error(err));
}

document.addEventListener("DOMContentLoaded", function() {
    const departamento = "<?php echo $nombre_departamento; ?>";
    const select = document.getElementById("departamento");
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].text === departamento) {
            select.selectedIndex = i;
            break;
        }
    }
});

</script>
</body>
</html>