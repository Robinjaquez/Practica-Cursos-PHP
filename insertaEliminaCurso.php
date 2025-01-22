<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 'admin') {
    header("Location: InicioSesion.php");
    exit();
}

$conexion = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar'])) {
        $codigo = (int)$_POST['codigo'];
        eliminarCurso($conexion, $codigo);
    }

    if (isset($_POST['agregar'])) {
        $nombre = $_POST['nombre'] ?? '';
        $plazas = (int)($_POST['plazas'] ?? 0);
        $plazo = $_POST['plazo'] ?? '';
        insertarCurso($conexion, $nombre, $plazas, $plazo);
    }
}

$cursos = sacarCursos($conexion);

function conectar()
{
    try {
        $con = new PDO('mysql:host=127.0.0.1;dbname=cursoscp', 'daw', '1234');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

function sacarCursos($con)
{
    $consulta = $con->query("SELECT * FROM cursos");
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

function eliminarCurso($con, $codigo)
{
    try {
        $consulta = $con->prepare("DELETE FROM cursos WHERE codigo = :codigo");
        $consulta->bindParam(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();
    } catch (PDOException $e) {
        echo "Error al eliminar el curso: " . $e->getMessage();
    }
}

function insertarCurso($con, $nombre, $plazas, $plazo)
{
    try {
        // Obtener el último valor de "codigo".
        $consultaCodigo = $con->query("SELECT MAX(codigo) AS max_codigo FROM cursos");
        $resultado = $consultaCodigo->fetch(PDO::FETCH_ASSOC);
        $nuevoCodigo = $resultado['max_codigo'] + 1;

        $consulta = $con->prepare("INSERT INTO cursos (codigo, nombre, numeroplazas, plazoinscripcion, abierto) VALUES (:codigo, :nombre, :plazas, :plazo, 1)");
        $consulta->bindParam(':codigo', $nuevoCodigo, PDO::PARAM_INT);
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindParam(':plazas', $plazas, PDO::PARAM_INT);
        $consulta->bindParam(':plazo', $plazo, PDO::PARAM_STR);
        $consulta->execute();
    } catch (PDOException $e) {
        echo "Error al insertar el curso: " . $e->getMessage();
    }
}

function mostrarCursos($cursos)
{
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Código</th>";
    echo "<th>Nombre</th>";
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
        echo "<td>" . ($curso['numeroplazas']) . "</td>";
        echo "<td>" . ($curso['plazoinscripcion']) . "</td>";
        echo "<td>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='codigo' value=' " . $curso['codigo'] . "'>";
        echo '<button type="submit" name="eliminar">Eliminar</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '        </tbody>';
    echo '    </table>';
}

function crearCursoForm()
{
    echo '    <h2 style="text-align: center;">Agregar Nuevo Curso</h2>';
    echo '    <form method="POST" style="text-align: center;">';
    echo '        <label for="nombre">Nombre del Curso:</label>';
    echo '        <input type="text" name="nombre" id="nombre" required>';
    echo '        <br><br>';
    echo '        <label for="plazas">Número de Plazas:</label>';
    echo '        <input type="number" name="plazas" id="plazas" required>';
    echo '        <br><br>';
    echo '        <label for="plazo">Plazo de Inscripción:</label>';
    echo '        <input type="date" name="plazo" id="plazo" required>';
    echo '        <br><br>';
    echo '        <button type="submit" name="agregar">Agregar Curso</button>';
    echo '    </form>';
}

echo '<!DOCTYPE html>';
echo '<html lang="es">';
echo '<head>';
echo '    <meta charset="UTF-8">';
echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '    <title>Gestionar Cursos</title>';
echo '    <link rel="stylesheet" href="styles.css">';
echo '</head>';
echo '<body>';
echo '    <h1 style="text-align: center;">Gestionar Cursos</h1>';
mostrarCursos($cursos);
crearCursoForm();
echo "<div style='text-align: center; margin-top: 10px;'>";
echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
echo "</div>";
echo '</body>';
echo '</html>';
