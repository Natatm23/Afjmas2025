<?php
// Conexión a SQL Server
$serverName = "10.10.1.144";
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

     //Traer el último número de solicitud
    $sql = "SELECT MAX(IdNumeroSolicitud) AS ultimo FROM AFM_Salida_Material"; 
    $stmt2 = sqlsrv_query($conn, $sql);
    if ($stmt2 && $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
        $nuevoConsecutivo = $row2['ultimo'] + 1;
    }


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salida de Material</title>
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

        h1 {
            margin-bottom: 16px;
        }

        .campo {
            display: flex;
            flex-direction: column;
        }

        .campo label {
            font-size: 14px;
            margin-bottom: 4px;
            color: #014070;
            font-weight: bold;
        }

        .campo input,
        .campo textarea,
        .campo select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

<!-- Drawer -->
<div class="drawer" id="drawer">
        <a href="dashboard.php" onclick="closeDrawer()"><i class="fas fa-home"></i> Inicio
        <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-truck-loading"></i>Salida de material</a>
        <a href="generar_reporte.php" onclick="closeDrawer()"><i class="fas fa-file-alt"></i>Generar reporte</a>
        <a href="editar_reporte.php"><i class="fas fa-edit"></i>Editar reporte</a>
        <a href="eliminar_reporte.php"><i class="fas fa-trash"></i>Eliminar reporte</a>
        <a href="salida_material.php" onclick="closeDrawer()"><i class="fas fa-arrow-alt-circle-right"></i>Mis salidas de material</a>
        <a href="#" onclick="closeDrawer()"><i class="fas fa-user"></i>Mi información</a>
        <a href="#" onclick="cerrarSesion(); closeDrawer();"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
    </div>


<div class="overlay" id="overlay" onclick="toggleDrawer()"></div>

<!-- Contenido -->
<div class="content">
    <h1 style="color: #014070; font-size: 22px; text-align: center; margin-bottom: 20px;">📝 Salida de Material</h1>

    <form id="formSalidaMaterial" onsubmit="agregarMaterial(); return false;" style="display: flex; flex-direction: column; gap: 16px;">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" required readonly>
        </div>

       <div class="campo">
    <label for="numero_solicitud">Número de Solicitud</label>
    <input type="text" id="IdNumeroSolicitud" value="<?php echo $nuevoConsecutivo; ?>" readonly>
</div>


        <div class="campo">
            <label for="departamento">Departamento</label>
            <select id="departamento" required>
                <option value="">Seleccione un departamento</option>
                <!-- Se llenará desde PHP -->
                <?php
                    include("obtener_departamentos.php");
                ?>
            </select>
        </div>

        <div class="campo">
            <label for="material">Codigo del material</label>
            <input type="text" id="material" required>
        </div>

        <div class="campo">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" required min="1" step="1">
        </div>

        <div class="campo">
            <label for="justificacion">Justificación</label>
            <textarea id="justificacion" rows="3" required></textarea>
        </div>

        <button type="submit" class="boton-agregar">➕ Agregar Material</button>
    </form>

    <h2 style="margin-top: 30px; font-size: 18px; color: #014070;">📦 Materiales Agregados</h2>

    <div style="overflow-x: auto; margin-top: 10px;">
        <table id="tablaMateriales">
    <thead style="background-color: #014070; color: white;">
        <tr>
            <th>Material</th>
            <th>Cantidad</th>
             <th>Justificación</th>  <!-- moví y cambié esta etiqueta, anteriormente era fecha-->
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <!-- Filas agregadas dinámicamente -->
    </tbody>
</table>
    </div>

    <button class="boton-generar" onclick="generarOrden()">🧾 Generar Orden de Salida</button>
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

    function cerrarSesion() {
        alert("Sesión cerrada correctamente");
        window.location.href = "login.php";
    }

  // Función para agregar material a la tabla
// Función para agregar material a la tabla
function agregarMaterial() {
    const material = document.getElementById("material").value;
    const cantidad = document.getElementById("cantidad").value;
    const justificacion = document.getElementById("justificacion").value;

    if (!material || !justificacion || !cantidad) {
        alert("Por favor completa todos los campos antes de agregar el material.");
        return;
    }

    const tabla = document.getElementById("tablaMateriales").getElementsByTagName('tbody')[0];
    const fila = tabla.insertRow();

    // Insertar datos en las celdas
    fila.insertCell(0).textContent = material;
    fila.insertCell(1).textContent = justificacion;
    fila.insertCell(2).textContent = cantidad;

    // Botón eliminar
    const celdaAccion = fila.insertCell(3);
    const botonEliminar = document.createElement("button");
    botonEliminar.innerText = "❌";
    botonEliminar.className = "boton-eliminar";
    botonEliminar.onclick = function () {
        tabla.deleteRow(fila.rowIndex - 1);
    };
    celdaAccion.appendChild(botonEliminar);

    // Limpiar campos y enfocar en material
    document.getElementById("material").value = "";
    document.getElementById("justificacion").value = "";
    document.getElementById("cantidad").value = "";
    document.getElementById("material").focus();
}

// Función para generar orden y enviar al servidor
function generarOrden() {
    const numeroSolicitud = document.getElementById("IdNumeroSolicitud").value;
    const departamento = document.getElementById("departamento").value;
    const fecha = document.getElementById("fecha").value;

    const filas = document.querySelectorAll("#tablaMateriales tbody tr");
    const formData = new FormData();

    formData.append("numeroSolicitud", numeroSolicitud);
    formData.append("departamento", departamento);
    formData.append("fecha", fecha);

    filas.forEach((fila, index) => {
        if (index < 10) { // máximo 10 materiales
            const celdas = fila.getElementsByTagName("td");
            formData.append("material" + (index + 1), celdas[0].textContent);
            formData.append("justificacion" + (index + 1), celdas[1].textContent);
            formData.append("cantidad" + (index + 1), celdas[2].textContent);
        }
    });

    fetch("insertar_solicitud.php", {
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
</script>
</body>
</html>

