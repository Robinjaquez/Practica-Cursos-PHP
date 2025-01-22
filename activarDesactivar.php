<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$conexion = conectar();

echo "<h1 style='text-align:center;'>Activar/Desactivar Cursos</h1>";
mostrarMensaje();
$cursos = sacarCursos($conexion);
generarTablaCursos($cursos);
mostrarBotonVolver();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'], $_POST['accion'])) {
    $codigo = $_POST['codigo'];
    $accion = $_POST['accion'];

    if ($accion === 'abrir') {
        $abierto = 1;
    } elseif ($accion === 'cerrar') {
        $abierto = 0;
    }

    $stmt = $conexion->prepare("UPDATE cursos SET abierto = :abierto WHERE codigo = :codigo");
    $stmt->bindParam(':abierto', $abierto);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();

    $_SESSION['mensaje'] = "El curso con código $codigo ha sido " . ($abierto ? "abierto" : "cerrado") . ".";
    header("Location: activarDesactivar.php");
    exit;
}
function sacarCursos($conexion)
{
    $consultaCursos = $conexion->query("SELECT * FROM cursos");
    return $consultaCursos->fetchAll(PDO::FETCH_ASSOC);
}


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

function generarTablaCursos($cursos)
{
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Código</th>";
    echo "<th>Nombre</th>";
    echo "<th>Abierto</th>";
    echo "<th>Número de Plazas</th>";
    echo "<th>Plazo de Inscripción</th>";
    echo "<th>Acción</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($cursos as $curso) {
        echo "<tr>";
        echo "<td>" . ($curso['codigo']) . "</td>";
        echo "<td>" . ($curso['nombre']) . "</td>";
        if ($curso['abierto'] == 1) {
            echo "<td>Si</td>";
        } else {
            echo "<td>No</td>";
        }
        echo "<td>" . ($curso['numeroplazas']) . "</td>";
        echo "<td>" . ($curso['plazoinscripcion']) . "</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='codigo' value='" . ($curso['codigo']) . "'>";
        echo "<button name='accion' value='abrir' " . ($curso['abierto'] == 1 ? 'disabled' : '') . ">Abrir</button>";
        echo "</form>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='codigo' value='" . ($curso['codigo']) . "'>";
        echo "<button name='accion' value='cerrar' " . ($curso['abierto'] == 0 ? 'disabled' : '') . ">Cerrar</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function mostrarMensaje()
{
    if (isset($_SESSION['mensaje'])) {
        echo "<p style='color:green;'>" . ($_SESSION['mensaje']) . "</p>";
        unset($_SESSION['mensaje']);
    }
}

function mostrarBotonVolver()
{
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}
