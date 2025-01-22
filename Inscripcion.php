<?php
session_start();

if (!isset($_SESSION['dni']) || empty($_SESSION['dni'])) {
    echo "<h2 style='text-align:center;'>Por favor, inicie sesión para inscribirse.</h2>";
    echo "<a href='InicioSesion.php' style='display: block; text-align: center;'>Iniciar Sesión</a>";
    exit;
}


if (isset($_GET['curso'])) {
    $codigoCurso = $_GET['curso'];
} else {
    echo "<h2 style='text-align:center;'>No se seleccionó un curso.</h2>";
    exit;
}


$conexion = conectar();

$curso = obtenerCursoPorCodigo($conexion, $codigoCurso);
if (!$curso || $curso['abierto'] == 0) {
    echo "<h2 style='text-align:center;'>El curso no está disponible para inscripción.</h2>";
    exit;
}

$inscrito = comprobarInscripcion($conexion, $_SESSION['dni'], $codigoCurso);
if ($inscrito) {
    echo "<h2 style='text-align:center;'>Ya estás inscrito en este curso.</h2>";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = $_SESSION['dni'];
    $fechaSolicitud = date('Y-m-d H:i:s');
    $admitido = 0;

    $query = "INSERT INTO solicitudes (dni, codigocurso, fechasolicitud, admitido) 
              VALUES (:dni, :codigocurso, :fechasolicitud, :admitido)";
    $consulta = $conexion->prepare($query);
    $consulta->execute([
        ':dni' => $dni,
        ':codigocurso' => $codigoCurso,
        ':fechasolicitud' => $fechaSolicitud,
        ':admitido' => $admitido
    ]);
    $_SESSION['mensaje'] = 'Te has inscrito correctamente.';
    header("Location: index.php");
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

function obtenerCursoPorCodigo($con, $codigo)
{
    $consulta = $con->prepare("SELECT * FROM cursos WHERE codigo = :codigo");
    $consulta->execute([':codigo' => $codigo]);
    return $consulta->fetch(PDO::FETCH_ASSOC);
}

function comprobarInscripcion($con, $dni, $codigoCurso)
{
    $consulta = $con->prepare("SELECT * FROM solicitudes WHERE dni = :dni AND codigocurso = :codigocurso");
    $consulta->execute([':dni' => $dni, ':codigocurso' => $codigoCurso]);
    return $consulta->fetch(PDO::FETCH_ASSOC);
}
?>

<?php
echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Inscripción a Curso</title>";
echo "<link rel='stylesheet' href='styles.css'>";
echo "</head>";
echo "<body>";

echo "<h1>Inscripción al curso: " . $curso['nombre'] . "</h1>";

echo "<p>Curso: " . $curso['nombre'] . "</p>";
echo "<p>Plazo de inscripción: " . $curso['plazoinscripcion'] . "</p>";

echo "<form method='POST'>";
echo "<div class='form-container'>";
echo "<button type='submit' class='button'>Inscribirse</button>";
echo "</div>";
echo "<div style='text-align: center; margin-top: 10px;'>";
echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
echo "</div>";
echo "</form>";


echo "</body>";
echo "</html>";
?>
