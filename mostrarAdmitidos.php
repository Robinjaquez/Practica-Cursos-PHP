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

function obtenerAdmitidos($conn, $codigocurso)
{
    $sql = "SELECT sol.dni, sol.nombre, sol.apellidos, sol.puntos
            FROM solicitudes s
            INNER JOIN solicitantes sol ON s.dni = sol.dni
            WHERE s.codigocurso = :codigocurso AND s.admitido = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codigocurso', $codigocurso);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarAdmitidos($admitidos)
{
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr><th>DNI</th>";
    echo "<th>Nombre</th>";
    echo "<th>Apellidos</th>";
    echo "<th>Puntos</th></tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($admitidos as $admitido) {
        echo "<tr>";
        echo "<td>" . $admitido['dni'] . "</td>";
        echo "<td>" . $admitido['nombre'] . "</td>";
        echo "<td>" . $admitido['apellidos'] . "</td>";
        echo "<td>" . $admitido['puntos'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}


if (isset($_GET['codigocurso'])) {
    $codigocurso = $_GET['codigocurso'];

    $conn = conectar();

    $admitidos = obtenerAdmitidos($conn, $codigocurso);


    mostrarAdmitidos($admitidos);
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
  
} else {
    echo "No se ha recibido un código de curso válido.";
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}
