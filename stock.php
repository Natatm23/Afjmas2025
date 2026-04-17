<?php
session_start();
@include_once('conexion_stock.php'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>STOCK</title>
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

/* === CONTENIDO PRINCIPAL === */
main.content {
    padding: 72px 16px 24px 16px;
    max-width: 1300px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;           /* Permite que los paneles bajen si no caben */
    gap: 20px;
    justify-content: center;
}

/* === PANEL / TARJETA === */
.dashboard-section {
    flex: 1 1 320px;           /* Se adapta según contenido */
    max-width: 480px;          /* Limita el tamaño máximo */
    background:#fff;
    border-radius:12px;
    padding:20px;
    box-shadow:0 6px 18px rgba(3,15,30,0.06);
    display: flex;
    flex-direction: column;
    transition: transform .2s ease, box-shadow .2s ease;
    word-wrap: break-word;
}
.dashboard-section:hover {
    transform: translateY(-3px);
    box-shadow:0 8px 22px rgba(3,15,30,0.12);
}
.dashboard-section h2 {
    color: rgb(1,62,112);
    margin-bottom:12px;
    display:flex;
    align-items:center;
    gap:8px;
    font-size: 18px;
}

/* === TABLA === */
.table-wrapper { overflow-x:auto; }
.stock-table { width:100%; border-collapse:collapse; }
.stock-table thead { background: rgb(1,62,112); color:#fff; }
.stock-table th, .stock-table td {
    padding:10px 12px;
    text-align:left;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.stock-table tr:nth-child(even){ background:#f6f9fc; }
.stock-table tr:hover{ background:#e9f4ff; }
.no-data { text-align:center; color:#777; padding:18px 0; }

/* === RESPONSIVE === */
@media (max-width: 760px) {
    main.content {
        flex-direction: column;
        align-items: stretch;
    }
    .dashboard-section {
        max-width: 100%;
        padding: 14px;
    }
    .stock-table th, .stock-table td {
        white-space: normal;
        font-size: 14px;
    }
}
   .stock-table {
    width: 100%;
    border-collapse: collapse;
}
.stock-table th, .stock-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

/* 🔴 Todos los materiales bajos se verán en rojo */
.stock-bajo {
    background-color: #f8d7da;  /* rojo claro */
    color: #721c24;             /* texto rojo oscuro */
    font-weight: bold;
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
    <a href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="#" onclick="cerrarSesion(); return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
</nav>

<!-- Overlay -->
<div id="overlay" class="overlay" role="button" aria-label="Cerrar menú"></div>

<main class="content" id="mainContent">

 <!-- 🔘 Botón de Reordenar -->
            </div>
        <button class="boton-generar" onclick="generarOrden()">🧾 Solicitar resurtimiento</button>
            </div>
            

<!-- Sección Material Bajo -->
<section class="dashboard-section">
    <div class="stock-container">
        <?php
        // Recuperar el valor de la unidad desde la sesión
        $unidad = isset($_SESSION['unidad']) ? trim($_SESSION['unidad']) : null;
        ?>
        <h2>
            <i class="fas fa-boxes"></i>
            <?php 
                if ($unidad) {
                    echo "Stock en unidad: <span style='color:rgb(1,62,112); font-weight:bold;'>$unidad</span>";
                } else {
                    echo "Stock en unidad (sin asignar)";
                }
            ?>
        </h2>

        <div class="table-wrapper">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Existencia</th>
                        <th>Cantidad máxima</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (isset($conn) && $conn) {
                    if ($unidad) {
                        $sql = "SELECT IdMaterial, Descripcion, Existencia, Maximo
                                FROM AFM_Stock_Vehiculos 
                                WHERE IdUnidad = ? 
                                ORDER BY Existencia ASC";
                        $params = array($unidad);
                        $result = sqlsrv_query($conn, $sql, $params);

                        if ($result && sqlsrv_has_rows($result)) {
                            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                $codigo = htmlspecialchars($row['IdMaterial']);
                                $desc   = htmlspecialchars($row['Descripcion']);
                                $cant   = (int)$row['Existencia'];
                                $maximo = htmlspecialchars($row['Maximo']);

                                // 🔴 Solo marcar en rojo si la existencia es menor a 5
                                $class = ($cant < 5) ? 'stock-bajo' : '';

                                echo "<tr class='{$class}'>
                                        <td>{$codigo}</td>
                                        <td title=\"{$desc}\">{$desc}</td>
                                        <td>{$cant}</td>
                                        <td>{$maximo}</td>
                                      </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="4" class="no-data">No hay materiales registrados.</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4" class="no-data">Unidad no definida en sesión.</td></tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<!--SCRIPT PARA HACER FUNCIONAR EL TOOLBAR-->
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

    console.log('Toolbar + Drawer activos');
});

function cerrarSesion(){
    if(confirm('¿Estás seguro de que quieres cerrar sesión?')){
        window.location.href='login.php';
    }
}
</script>