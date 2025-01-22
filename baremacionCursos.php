<?php
session_start();

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


function obtenerCursos($conn)
{
    $sql = "SELECT * FROM cursos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarCursos($conn)
{
    $cursos = obtenerCursos($conn);
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Código</th>";
    echo "<th>Nombre</th>";
    echo "<th>Acción</th>";
    echo "</tr>";
    echo "</thead>";

    foreach ($cursos as $curso) {
        echo "<tr>";
        echo "<td>" . $curso['codigo'] . "</td>";
        echo "<td>" . $curso['nombre'] . "</td>";
        echo "<td><form  action='baremacionCursos.php' method='POST'>";
        echo "<input type='hidden' name='codigocurso' value='" . $curso['codigo'] . "'>";
        echo "<button type='submit'>Solicitar Admitidos</button>";
        echo "</form></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}


function obtenerSolicitudes($conn, $codigocurso)
{
    $sql = "SELECT s.dni, s.codigocurso, s.fechasolicitud, s.admitido, sol.nombre, sol.apellidos, sol.puntos 
            FROM solicitudes s 
            INNER JOIN solicitantes sol ON s.dni = sol.dni 
            WHERE s.admitido = 0 AND s.codigocurso = :codigocurso
            ORDER BY s.codigocurso, sol.puntos DESC";

    $consulta = $conn->prepare($sql);
    $consulta->bindParam(':codigocurso', $codigocurso);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

function verificarAdmitidoEnOtrosCursos($conn, $dni, $codigocurso)
{
    $sql = "SELECT 1 FROM solicitudes WHERE dni = :dni AND admitido = 1 AND codigocurso != :codigocurso";
    $consulta = $conn->prepare($sql);
    $consulta->bindParam(':dni', $dni);
    $consulta->bindParam(':codigocurso', $codigocurso);
    $consulta->execute();
    return $consulta->rowCount() > 0;
}

function admitirSolicitante($conn, $dni, $codigocurso)
{
    $sql = "UPDATE solicitudes SET admitido = 1 WHERE dni = :dni AND codigocurso = :codigocurso";
    $consulta = $conn->prepare($sql);
    $consulta->bindParam(':dni', $dni);
    $consulta->bindParam(':codigocurso', $codigocurso);
    $consulta->execute();
}

function actualizarPlazas($conn, $codigocurso)
{
    $sql = "SELECT numeroplazas FROM cursos WHERE codigo = :codigocurso";
    $consulta = $conn->prepare($sql);
    $consulta->bindParam(':codigocurso', $codigocurso);
    $consulta->execute();
    $curso = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($curso && $curso['numeroplazas'] > 0) {
        $plazas = $curso['numeroplazas'] - 1;
        $sqlUpdatePlazas = "UPDATE cursos SET numeroplazas = :plazas WHERE codigo = :codigocurso";
        $consultaUpdatePlazas = $conn->prepare($sqlUpdatePlazas);
        $consultaUpdatePlazas->bindParam(':plazas', $plazas);
        $consultaUpdatePlazas->bindParam(':codigocurso', $codigocurso);
        $consultaUpdatePlazas->execute();
    }
}

function procesarSolicitudes($conn, $solicitudesArray, $codigocurso)
{
    foreach ($solicitudesArray as $index => $solicitud) {
        $dni = $solicitud['dni'];

        // Si el solicitante ya está admitido en otro curso, moverlo al final
        if (verificarAdmitidoEnOtrosCursos($conn, $dni, $codigocurso)) {
            $solicitudesArray[] = $solicitud;
            unset($solicitudesArray[$index]);
        }
    }

    $solicitudesArray = array_values($solicitudesArray);

    foreach ($solicitudesArray as $solicitud) {
        $dni = $solicitud['dni'];

        // Verificar cuántas plazas quedan
        $sqlPlazas = "SELECT numeroplazas FROM cursos WHERE codigo = :codigocurso";
        $consultaPlazas = $conn->prepare($sqlPlazas);
        $consultaPlazas->bindParam(':codigocurso', $codigocurso);
        $consultaPlazas->execute();
        $curso = $consultaPlazas->fetch(PDO::FETCH_ASSOC);

        // Verificar si hay plazas disponibles
        if ($curso && $curso['numeroplazas'] > 0) {
            admitirSolicitante($conn, $dni, $codigocurso);
            actualizarPlazas($conn, $codigocurso);
        }
    }
}

// Si se ha seleccionado un curso, procesar las solicitudes
if (isset($_POST['codigocurso'])) {
    $codigocurso = $_POST['codigocurso'];
    $conn = conectar();

    $solicitudesArray = obtenerSolicitudes($conn, $codigocurso);

    procesarSolicitudes($conn, $solicitudesArray, $codigocurso);
    //este lo he tenido que poner con url tambien porque sino no me dejaba ya que necesito el codigo del curso..
    header("Location: mostrarAdmitidos.php?codigocurso=$codigocurso");
} else {
    $conn = conectar();
    mostrarCursos($conn);
}
