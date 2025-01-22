<?php
session_start();

if (isset($_SESSION['error']) && $_SESSION['error'] === 'solicitante') {
    $dni = $_SESSION['dniError'];
    echo "<h2 style='color:red;'>Error al crear solicitante :( </h2>";
    registroTodo($dni);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    if (empty($dni)) {
        echo "El campo DNI es obligatorio.";
        exit;
    }

    try {
        $conexion = conectar();
        $dniValido = dniRegistrado($conexion, $dni);

        if ($dniValido) {
            $_SESSION['dni'] = $dni;
            $_SESSION['solicitante'] = "true";
            header("Location: index.php");
            exit;
        } else {
            registroTodo($dni);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    registroDNI();
}


function registroDNI()
{
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<form action='registro.php' method='post'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Campo</th>";
    echo "<th>Valor</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    pintaInputs("Introduce tu DNI", "dni", "text");
    echo "<tr>";
    echo "<td colspan='2' align='center'>";
    echo "<button type='submit'>Registro:</button>";
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</form>";
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}



function registroTodo($dni)
{
    echo "<form action='crearSolicitante.php' method='post'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Campo</th>";
    echo "<th>Valor</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr>";
    echo "<td>Dni: </td>";
    echo "<td><input type='text' name='dni' value='$dni'required></td>";
    echo "</tr>";
    pintaInputsRequeridos("Apellidos", "apellidos", "text");
    pintaInputsRequeridos("Nombre", "nombre", "text");
    pintaInputsRequeridos("Telefono", "telefono", "number");
    pintaInputsRequeridos("Correo", "correo", "text");
    pintaInputsRequeridos("Codigo Centro", "codcen", "text");
    pintaInputs("Cordinador Tic", "coordinadortic", "checkbox");
    pintaInputs("Grupo Tic", "grupotic", "checkbox");
    pintaInputs("Nombre De Grupo", "nomgrupo", "text");
    pintaInputs("Programa bilingüe", "pbilin", "checkbox");
    pintaInputs("Cargo", "cargo", "checkbox");
    pintaInputs("Nombre Del Cargo", "nombrecargo", "text");
    pintaSelect();
    pintaInputsRequeridos("Fecha De Nacimiento", "fechanac", "date");
    pintaInputs("Especialidad", "especialidad", "text");
    echo "<tr>";
    echo "<td colspan='2' align='center'>";
    echo "<button type='submit'>Registro</button>";
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</form>";
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}

function pintaInputs($tituloInput, $nombreInput, $tipo)
{
    echo "<tr>";
    echo "<td>$tituloInput: </td>";
    echo "<td><input type='$tipo' name='$nombreInput'></td>";
    echo "</tr>";
}

function pintaInputsRequeridos($tituloInput, $nombreInput, $tipo)
{
    echo "<tr>";
    echo "<td>$tituloInput: </td>";
    echo "<td><input type='$tipo' name='$nombreInput' required></td>";
    echo "</tr>";
}

function pintaSelect()
{
    echo "<tr>";
    echo "<td>";
    echo "<label>Situación:</label>";
    echo "</td>";
    echo "<td>";
    echo "<select id='situacion' name='situacion'>";
    echo "<option value='activo'>Activo</option>";
    echo "<option value='inactivo'>Inactivo</option>";
    echo "</td>";
    echo "</tr>";
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


function dniRegistrado($conexion, $dni)
{
    try {
        $consulta = $conexion->prepare("SELECT dni FROM `solicitantes` WHERE dni = :dni");
        $consulta->bindParam(':dni', $dni, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado !== false; // Retorna true si hay un registro, false si no
    } catch (PDOException $e) {
        error_log("Error en la consulta: " . $e->getMessage());
        return false;
    }
}
