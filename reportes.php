<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard AFFIJO2025</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI',sans-serif;
    background:#f5f7fa;
    overflow-x:hidden;
    color:#222;
}

/*=========================
  TOOLBAR
=========================*/
.toolbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:55px;
    background:rgb(1,62,112);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 16px;
    z-index:1000;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
}

.menu-icon,
.logout-button{
    background:none;
    border:none;
    color:#fff;
    font-size:22px;
    cursor:pointer;
}

.logo-container{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.logo-container img{
    height:34px;
}

/*=========================
  DRAWER
=========================*/
.drawer{
    position:fixed;
    top:0;
    left:-260px;
    width:260px;
    height:100%;
    background:#fff;
    z-index:999;
    padding-top:55px;
    transition:.25s ease;
    box-shadow:4px 0 12px rgba(0,0,0,.12);
}

.drawer.open{
    left:0;
}

.drawer a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    color:#333;
    text-decoration:none;
    border-bottom:1px solid #eee;
    transition:.2s;
}

.drawer a:hover{
    background:#f3f6fa;
    color:rgb(1,62,112);
}

/*=========================
  OVERLAY
=========================*/
.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    display:none;
    z-index:998;
}

.overlay.show{
    display:block;
}

/*=========================
  TABS
=========================*/
.tabs{
    margin-top:55px;
    display:flex;
    background:#fff;
    border-bottom:2px solid rgb(1,62,112);
}

.tab{
    flex:1;
    border:none;
    background:#fff;
    padding:14px;
    cursor:pointer;
    font-weight:600;
    color:rgb(1,62,112);
    transition:.2s;
}

.tab i{
    margin-right:6px;
}

.tab:hover{
    background:#eef4f8;
}

.tab.active{
    background:rgb(15,94,150);
    color:#fff;
}

/*=========================
  CONTENIDO
=========================*/
.tab-content{
    display:none;
    padding:22px;
}

.tab-content.active{
    display:block;
}

.contenedor{
    max-width:1300px;
    margin:auto;
}

/*=========================
  FORMULARIO
=========================*/
.formulario{
    display:grid;
    grid-template-columns:1fr 1fr auto;
    gap:18px;
    align-items:end;
    margin-bottom:25px;
}

.campo{
    display:flex;
    flex-direction:column;
}

.campo label{
    font-size:13px;
    font-weight:700;
    color:rgb(1,62,112);
    margin-bottom:7px;
}

.campo input[type="date"]{
    height:50px;
    border:1px solid #d7dfe8;
    border-radius:14px;
    padding:0 14px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:#fff;
    box-shadow:0 6px 12px rgba(0,0,0,.04);
}

.campo input[type="date"]:focus{
    border-color:rgb(15,94,150);
    box-shadow:0 0 0 4px rgba(15,94,150,.12);
}

.formulario button{
    height:50px;
    border:none;
    border-radius:14px;
    padding:0 28px;
    background:linear-gradient(135deg,rgb(1,62,112),rgb(15,94,150));
    color:#fff;
    font-weight:700;
    cursor:pointer;
    transition:.2s;
    box-shadow:0 10px 18px rgba(1,62,112,.18);
}

.formulario button:hover{
    transform:translateY(-2px);
}

/*=========================
  TABLA
=========================*/
.tabla{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 8px 18px rgba(0,0,0,.05);
}

.tabla th{
    background:rgb(1,62,112);
    color:#fff;
    padding:14px;
    text-align:left;
}

.tabla td{
    padding:14px;
    border-bottom:1px solid #eee;
}

.tabla tr:hover{
    background:#f8fbff;
}

/*=========================
  RESPONSIVE
=========================*/
@media(max-width:768px){

    .tabs{
        flex-direction:column;
    }

    .formulario{
        grid-template-columns:1fr;
    }

    .formulario button{
        width:100%;
    }

    .tab{
        text-align:left;
    }
}
/* GRID DEL FORMULARIO */
.formulario{
    display:grid;
    grid-template-columns:1fr 1fr auto auto;
    gap:18px;
    align-items:end;
    margin-bottom:25px;
}

/* HORAS */
.horas-radio{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    min-height:50px;
    align-items:center;
}

.radio-item{
    position:relative;
    cursor:pointer;
}

.radio-item input{
    display:none;
}

.radio-item span{
    display:flex;
    align-items:center;
    justify-content:center;
    min-width:78px;
    height:50px;
    padding:0 14px;
    border:1px solid #d7dfe8;
    border-radius:14px;
    background:#fff;
    font-size:14px;
    font-weight:700;
    color:rgb(1,62,112);
    transition:.25s ease;
    box-shadow:0 6px 12px rgba(0,0,0,.04);
}

.radio-item span:hover{
    border-color:rgb(15,94,150);
}

.radio-item input:checked + span{
    background:linear-gradient(135deg,rgb(1,62,112),rgb(15,94,150));
    color:#fff;
    border-color:rgb(15,94,150);
    box-shadow:0 10px 18px rgba(1,62,112,.18);
}

/* RESPONSIVO */
@media(max-width:768px){
    .formulario{
        grid-template-columns:1fr;
    }

    .horas-radio{
        width:100%;
    }

    .radio-item{
        flex:1;
    }

    .radio-item span{
        width:100%;
    }
}

</style>
</head>
<body>

<!-- TOOLBAR -->
<div class="toolbar">

    <button class="menu-icon" id="menuToggle">
        ☰
    </button>

    <div class="logo-container">
        <img src="JMAS blanco.png" alt="Logo">
    </div>

    <button class="logout-button" onclick="cerrarSesion()">
        <i class="fas fa-sign-out-alt"></i>
    </button>

</div>

<!-- Drawer -->
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

<div class="overlay" id="overlay"></div>

<!-- TABS -->
<div class="tabs">

    <button class="tab active" onclick="cambiarTab(event,'reconecciones')">
        <i class="fas fa-file-alt"></i> Reconecciones
    </button>

    <button class="tab" onclick="cambiarTab(event,'cortes')">
        <i class="fas fa-chart-line"></i> Cortes
    </button>

    <button class="tab" onclick="cambiarTab(event,'resumen')">
        <i class="fas fa-box"></i> Resumen
    </button>

</div>

<!-- TAB 1 -->
<div id="reconecciones" class="tab-content active">
<div class="contenedor">

    <div class="formulario">

    <div class="campo">
        <label>Fecha Inicio</label>
        <input type="date" id="fechaInicio">
    </div>

    <div class="campo">
        <label>Fecha Fin</label>
        <input type="date" id="fechaFin">
    </div>

    <div class="campo">
        <label>Hora</label>

        <div class="horas-radio">

            <label class="radio-item">
                <input type="radio" name="hora" value="12:30">
                <span>12:30</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="hora" value="13:30">
                <span>13:30</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="hora" value="15:00">
                <span>15:00</span>
            </label>

        </div>
    </div>

    <button onclick="buscarFechas()">Buscar</button>

</div>

    <table class="tabla" id="tablaDatos">
        <thead>
            <tr>
                <th>Cuenta</th>
                <th>Ruta</th>
                <th>Dirección</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

</div>
</div>

<!-- TAB 2 -->
<div id="cortes" class="tab-content">
<div class="contenedor">
    <h2>Reporte de Cortes</h2>
    <br>
    <p>Aquí aparecerá la información.</p>
</div>
</div>

<!-- TAB 3 -->
<div id="resumen" class="tab-content">
<div class="contenedor">
    <h2>Resumen General</h2>
    <br>
    <p>Indicadores o estadísticas.</p>
</div>
</div>

<script>
/*=========================
DRAWER
=========================*/
const drawer = document.getElementById('drawer');
const overlay = document.getElementById('overlay');
const menuToggle = document.getElementById('menuToggle');

menuToggle.addEventListener('click',()=>{
    drawer.classList.toggle('open');
    overlay.classList.toggle('show');
});

overlay.addEventListener('click',()=>{
    drawer.classList.remove('open');
    overlay.classList.remove('show');
});

/*=========================
TABS
=========================*/
function cambiarTab(e,id){

    document.querySelectorAll('.tab')
    .forEach(tab=>tab.classList.remove('active'));

    document.querySelectorAll('.tab-content')
    .forEach(c=>c.classList.remove('active'));

    e.currentTarget.classList.add('active');
    document.getElementById(id).classList.add('active');
}

/*=========================
LOGOUT
=========================*/
function cerrarSesion(){
    if(confirm('¿Cerrar sesión?')){
        window.location.href='login.php';
    }
}

/*=========================
BUSCAR FECHAS
=========================*/
function buscarFechas(){

    const inicio = document.getElementById('fechaInicio').value;
    const fin = document.getElementById('fechaFin').value;

    if(inicio === '' || fin === ''){
        alert('Selecciona ambas fechas');
        return;
    }

    const tbody = document.querySelector('#tablaDatos tbody');
    tbody.innerHTML = '';

    for(let i=1;i<=5;i++){

        let fila = `
        <tr>
            <td>000${i}</td>
            <td>Ruta ${i}</td>
            <td>Dirección ejemplo ${i}</td>
        </tr>`;

        tbody.innerHTML += fila;
    }
}

/*=========================
FECHA ACTUAL AUTOMÁTICA
=========================*/
window.addEventListener('DOMContentLoaded', () => {

    const hoy = new Date();

    const año = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');

    const fechaActual = `${año}-${mes}-${dia}`;

    document.getElementById('fechaInicio').value = fechaActual;
    document.getElementById('fechaFin').value = fechaActual;

});

</script>

</body>
</html>