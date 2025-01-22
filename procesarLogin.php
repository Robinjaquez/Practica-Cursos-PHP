<?php
session_start();

$conexion = conectar();
validarUsuario($conexion);


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

function validarUsuario($conexionBD)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        try {
            $validarUser = $conexionBD->prepare(
                "SELECT * FROM `administradores` WHERE `user` = :username AND `password` = :password"
            );
            $validarUser->bindParam(':username', $username);
            $validarUser->bindParam(':password', $password);
            $validarUser->execute();

            $valido = $validarUser->fetch(PDO::FETCH_ASSOC);

            if ($valido) {
                $_SESSION['is_admin'] = 'admin';
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos.";
                header("Location: InicioSesion.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al procesar la solicitud. Inténtalo más tarde.";
            error_log("Error en la validación de usuario: " . $e->getMessage());
            header("Location: InicioSesion.php");
            exit;
        }
    }
}
