<?php

session_start();
createLoginForm();

function createLoginForm()
{
    if (!empty($_SESSION['error'])) {
        echo "<p style='color: red; text-align: center;'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    function pintaInputs($tituloInput, $nombreInput, $tipo)
    {
        echo "<tr>";
        echo "<td>$tituloInput: </td>";
        echo "<td><input type='$tipo' name='$nombreInput' required></td>";
        echo "</tr>";
    }
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<form action='procesarLogin.php' method='post'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Campo</th>";
    echo "<th>Valor</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    pintaInputs("Nombre de Usuario","username","text");
    pintaInputs("Contraseña","password","password" );
    echo "<tr>";
    echo "<td colspan='2' align='center'>";
    echo "<button type='submit'>Iniciar Sesión</button>";
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</form>";
    echo "<div style='text-align: center; margin-top: 10px;'>";
    echo "<a href='index.php' style='text-decoration: none;'><button type='button'>Volver</button></a>";
    echo "</div>";
}
