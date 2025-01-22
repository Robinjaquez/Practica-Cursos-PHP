<?php

session_start();

$puntos = 0;

if (isset($_POST['coordinadortic'])) {
    $puntos += 4;
}
if (isset($_POST['grupotic'])) {
    $puntos += 3;
}
if (isset($_POST['pbilin'])) {
    $puntos += 3;
}
if (isset($_POST['cargo'])) {
    switch ($_POST['nombrecargo']) {
        case 'director':
            $puntos += 2;
            break;
        case 'jefe_estudios':
            $puntos += 2;
            break;
        case 'secretario':
            $puntos += 2;
            break;
        case 'jefe_departamento':
            $puntos += 1;
            break;
    }
}
if (isset($_POST['antiguedad']) && intval($_POST['antiguedad']) >= 15) {
    $puntos += 1;
}
if (!empty($_POST['situacion']) && $_POST['situacion'] == 'activo') {
    $puntos += 1;
}


if (empty($_POST['dni']) || empty($_POST['nombre']) || empty($_POST['apellidos'])) {
    echo "Error: DNI, nombre y apellidos son obligatorios.";
    exit;
}

$datos = [
    'dni' => trim($_POST['dni']),
    'apellidos' => trim($_POST['apellidos']),
    'nombre' => trim($_POST['nombre']),
    'telefono' => intval($_POST['telefono']),
    'correo' => trim($_POST['correo']),
    'codcen' => trim($_POST['codcen']),
    'coordinadortic' => isset($_POST['coordinadortic']) ? 1 : 0,
    'grupotic' => isset($_POST['grupotic']) ? 1 : 0,
    'nomgrupo' => trim($_POST['nomgrupo']),
    'pbilin' => isset($_POST['pbilin']) ? 1 : 0,
    'cargo' => isset($_POST['cargo']) ? 1 : 0,
    'nomcargo' => trim($_POST['nombrecargo']),
    'situacion' => trim($_POST['situacion']),
    'fechanac' => trim($_POST['fechanac']),
    'especialidad' => trim($_POST['especialidad']),
    'puntos' => $puntos,
];

try {
    $conexion = conectar();
    crearSolicitante($conexion, $datos);
    $_SESSION['solicitante'] = 'true';
    $_SESSION['dni'] = $_POST['dni'];
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Error al procesar la solicitud: " . $e->getMessage();
    header("Location: registro.php");
    exit;
}

function crearSolicitante($conexion, $datos)
{
    try {
        $consulta = $conexion->prepare(
            "INSERT INTO solicitantes (
                dni, apellidos, nombre, telefono, correo, codcen, 
                coordinadortic, grupotic, nomgrupo, pbilin, cargo, nombrecargo, 
                situacion, fechanac, especialidad, puntos
            ) VALUES (
                :dni, :apellidos, :nombre, :telefono, :correo, :codcen, 
                :coordinadortic, :grupotic, :nomgrupo, :pbilin, :cargo, :nombrecargo, 
                :situacion, :fechanac, :especialidad, :puntos
            )"
        );

        $consulta->bindParam(':dni', $datos['dni'], PDO::PARAM_STR);
        $consulta->bindParam(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
        $consulta->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $consulta->bindParam(':telefono', $datos['telefono'], PDO::PARAM_INT);
        $consulta->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
        $consulta->bindParam(':codcen', $datos['codcen'], PDO::PARAM_STR);
        $consulta->bindParam(':coordinadortic', $datos['coordinadortic'], PDO::PARAM_INT);
        $consulta->bindParam(':grupotic', $datos['grupotic'], PDO::PARAM_INT);
        $consulta->bindParam(':nomgrupo', $datos['nomgrupo'], PDO::PARAM_STR);
        $consulta->bindParam(':pbilin', $datos['pbilin'], PDO::PARAM_INT);
        $consulta->bindParam(':cargo', $datos['cargo'], PDO::PARAM_INT);
        $consulta->bindParam(':nombrecargo', $datos['nomcargo'], PDO::PARAM_STR);
        $consulta->bindParam(':situacion', $datos['situacion'], PDO::PARAM_STR);
        $consulta->bindParam(':fechanac', $datos['fechanac'], PDO::PARAM_STR);
        $consulta->bindParam(':especialidad', $datos['especialidad'], PDO::PARAM_STR);
        $consulta->bindParam(':puntos', $datos['puntos'], PDO::PARAM_INT);

        $consulta->execute();
    } catch (PDOException $e) {
        throw new Exception("Error al crear solicitante: " . $e->getMessage());
    }
}

function conectar()
{
    try {
        $con = new PDO('mysql:host=127.0.0.1;dbname=cursoscp', 'daw', '1234');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    } catch (PDOException $e) {
        throw new Exception("Error de conexión: " . $e->getMessage());
    }
}

// function validarFormulario($data) {
    //if (empty($data['dni']) || !preg_match('/^[0-9]{8}[A-Za-z]{1}$/', $data['dni'])) {
    //    return "DNI no es válido";
   //   }
  //    if (empty($data['correo']) || !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
   //       return "Correo electrónico no es válido";
   //   }
    //  if (empty($data['telefono']) || !preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/', $data['telefono'])) {
   //       return "Teléfono no es válido";
   //   }
  //    if (empty($data['fechanac'])) {
  //        return "La fecha de nacimiento es obligatoria";
  //    }
  //    return true; 
 // }
