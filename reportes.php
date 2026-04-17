<?php
session_start();

// Validación básica de sesión (opcional)
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Dashboard AFFIJO2025</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Segoe UI', sans-serif;
    background:#f9f9f9;
    overflow-x: hidden;
}

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

.menu-icon {
    font-size:24px;
    cursor:pointer;
    background:none;
    border:none;
    color:white;
}

.logo-container {
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.logo-container img {
    height:35px;
}

.logout-button {
    background:none;
    border:none;
    color:white;
    font-size:20px;
    cursor:pointer;
}

/* Drawer */
.drawer {
    position: fixed;
    top: 0;
    left: -260px;
    width: 260px;
    height: 100%;
    background:#fff;
    box-shadow:2px 0 8px rgba(0,0,0,0.12);
    padding-top:56px;
    transition: left .28s ease;
    z-index: 1001;
}

.drawer.open { left: 0; }

.drawer a {
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    color:#333;
    text-decoration:none;
    border-bottom:1px solid #eee;
}

.drawer a:hover {
    background:#f5f5f5;
}

/* Overlay */
.overlay {
    position: fixed;
    inset:0;
    background: rgba(0,0,0,0.32);
    display: none;
    z-index: 1000;
}

.overlay.show { display:block; }

/* CONTENIDO */
.contenedor {
    margin-top: 80px;
    padding: 20px;
}

/* Formulario */
.formulario {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.formulario input,
.formulario select {
    padding: 8px;
    font-size: 14px;
    border:1px solid #ccc;
    border-radius:4px;
}

.formulario button {
    background: rgb(1,62,112);
    color: white;
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    border-radius:4px;
}

.formulario button:hover {
    background: rgb(0,45,85);
}

/* Tabla */
.tabla {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius:6px;
    overflow:hidden;
}

.tabla th {
    background: rgb(1,62,112);
    color: white;
    padding: 10px;
}

.tabla td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.tabla tr:hover {
    background: #f5f5f5;
}
</style>
</head>

<body>

<!-- Toolbar -->
<div class="toolbar">
    <button class="menu-icon" id="menuToggle">☰</button>

    <div class="logo-container">
        <img src="JMAS blanco.png" class="logo-img" alt="Logo">
    </div>

    <button class="logout-button" onclick="cerrarSesion()">
        <i class="fas fa-sign-out-alt"></i>
    </button>
</div>

<div class="drawer" id="drawer">
        <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
    <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
    <a href="salida_material.php"><i class="fas fa-truck-loading"></i> Salida de material</a>
    <a href="devolucion_material.php"><i class="fas fa-truck fa-flip-horizontal"></i> Devolución de material</a>
    <a href="generar_reporte.php"><i class="fas fa-file-alt"></i> Generar reporte</a>
    <a href="editar_reporte.php"><i class="fas fa-edit"></i> Editar reporte</a>
    <a href="eliminar_reporte.php"><i class="fas fa-trash"></i> Eliminar reporte</a>
    <a href="stock.php"><i class="fas fa-boxes"></i> Stock</a>
    <a href="#"><i class="fas fa-user"></i> Mi información</a>
    <a href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="#" onclick="cerrarSesion(); return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
</nav>

<div id="overlay" class="overlay"></div>

<!-- CONTENIDO -->
<div class="contenedor">

    <div class="formulario">
        <input type="text" id="txtBuscar" placeholder="Escribe algo...">

        <select id="comboOpciones">
            <option value="">Selecciona una opción</option>
            <option value="1">Opción 1</option>
            <option value="2">Opción 2</option>
            <option value="3">Opción 3</option>
        </select>

        <button onclick="agregarFila()">Agregar</button>
    </div>

    <table class="tabla" id="tablaDatos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Texto</th>
                <th>Opción</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>1</td><td>Ejemplo 1</td><td>Opción 1</td></tr>
            <tr><td>2</td><td>Ejemplo 2</td><td>Opción 2</td></tr>
            <tr><td>3</td><td>Ejemplo 3</td><td>Opción 3</td></tr>
        </tbody>
    </table>

</div>

<script>
// Drawer
const drawer = document.getElementById('drawer');
const overlay = document.getElementById('overlay');
const menuToggle = document.getElementById('menuToggle');

menuToggle.addEventListener('click', () => {
    drawer.classList.toggle('open');
    overlay.classList.toggle('show');
});

overlay.addEventListener('click', () => {
    drawer.classList.remove('open');
    overlay.classList.remove('show');
});

// Agregar fila
function agregarFila(){
    const texto = document.getElementById('txtBuscar').value;
    const combo = document.getElementById('comboOpciones');
    const opcion = combo.options[combo.selectedIndex].text;

    if(texto === "" || combo.value === ""){
        alert("Completa todos los campos");
        return;
    }

    const tabla = document.getElementById('tablaDatos').getElementsByTagName('tbody')[0];
    const nuevaFila = tabla.insertRow();
    const id = tabla.rows.length;

    nuevaFila.insertCell(0).innerText = id;
    nuevaFila.insertCell(1).innerText = texto;
    nuevaFila.insertCell(2).innerText = opcion;

    document.getElementById('txtBuscar').value = "";
    combo.selectedIndex = 0;
}

// Logout
function cerrarSesion(){
    if(confirm('¿Cerrar sesión?')){
        window.location.href='logout.php';
    }
}
</script>

</body>
</html>