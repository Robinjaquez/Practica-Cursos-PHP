<?php
session_start();
$esAdmin = false;
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 'admin') {
    echo "<h2 style='text-align:right; font-size: 14px; padding-right: 40px;'>Bienvenido Administrador | <a href='cerrarSesion.php'>Cerrar sesión</a></h2>";
    $esAdmin = true;
} else {
    echo "<h2 style='text-align:right; font-size: 14px; padding-right: 40px;'><a href='InicioSesion.php'>Iniciar Sesión</a></h2>";
    $esAdmin = false;
}

// Mostrar el título de la página
echo "<h1 style='text-align:center;'>Listado de Admitidos:</h1>";

if ($esAdmin) {
    // Conexión a la base de datos
    $conexion = conectar();
    // Obtener los admitidos
    $admitidos = obtenerAdmitidos($conexion);
    // Mostrar los admitidos
    mostrarAdmitidos($admitidos);
}
echo "<div style='text-align: center; margin-top: 10px;'>";
echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
echo "</div>";


function conectar()
{
    try {
        $con = new PDO('mysql:host=127.0.0.1;dbname=cursoscp', 'daw', '1234');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function obtenerAdmitidos($con)
{
    $consultaAdmitidos = $con->query("SELECT dni, codigocurso, fechasolicitud FROM solicitudes WHERE admitido = 1");
    return $consultaAdmitidos->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarAdmitidos($admitidos)
{
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>DNI</th>";
    echo "<th>Código del Curso</th>";
    echo "<th>Fecha de Solicitud</th>";
    echo "</tr>";
    echo "</thead>";

    if ($admitidos) {
        foreach ($admitidos as $admitido) {
            echo "<tr>";
            echo "<td>" . ($admitido['dni']) . "</td>";
            echo "<td>" . ($admitido['codigocurso']) . "</td>";
            echo "<td>" . ($admitido['fechasolicitud']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No hay estudiantes admitidos</td></tr>";
    }
    echo "</table>";
}
