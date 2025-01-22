
</script>
<!-- Pon el codigo aqui:
 <script>
window.addEventListener('unload', () => {
    navigator.sendBeacon('cerrarSesion.php');
}); -->
<?php
/*  El codigo de arriba lo tengo puesto para que se pueda cerrar la sesion al recargar la pagina o al salir de ella,
    porque si queria probar con diferentes dni`s o hacer pruebas, me molestaba ya que paso varias veces por el index. 
    Si no lo necesitas puedes comentarlo y asi no molesta o usar la pagina asi :)
    */

session_start();
$esAdmin = false;
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 'admin') {
    echo "<h2 style='text-align:right; font-size: 14px; padding-right: 40px;'>Bienvenido Administrador | <a href='cerrarSesion.php'>Cerrar sesión</a></h2>";
    $esAdmin = true;
} else {
    echo "<h2 style='text-align:right; font-size: 14px; padding-right: 40px;'><a href='InicioSesion.php'>Iniciar Sesión</a></h2>";
    $esAdmin = false;
}

/**
 * Aplicación principal:
 * 1. Crear la conexión con la base de datos.
 * 2. Ejecutar la consulta de selección de cursos abiertos.
 * 3. Mostrar la tabla de cursos para todos los usuarios.
 * 4. Mostrar botones adicionales si un administrador ha iniciado sesión.
 */


echo "<h1 style='text-align:center;'>Lista de Cursos:</h1>";
$conexion = conectar();
$cursos = sacarCursos($conexion);
mostrarCursos($cursos, $esAdmin);
mostrarMensaje();





if ($esAdmin) {
    mostrarOpcionesAdministrador();
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

function sacarCursos($con)
{
    $consultaCursos = $con->query("SELECT * FROM cursos WHERE abierto = 1");
    return $consultaCursos->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarCursos($cursos, $admin)
{
    
    $inscripcion = isset($_SESSION['solicitante']) && $_SESSION['solicitante'] === 'true';
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table border='1' align='center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Código</th>";
    echo "<th>Nombre</th>";
    echo "<th>Abierto</th>";
    echo "<th>Número de Plazas</th>";
    echo "<th>Plazo de Inscripción</th>";
    if (!$admin) {
        echo "<th>Acción</th>";
    }
    echo "</tr>";
    echo "</thead>";

    if ($cursos) {
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

            if (!$admin) {
                if ($inscripcion) {
                    //este lo he tenido que poner con url porque sino no me dejaba , aunque queda un poco feo me ahorro no hacer un formulario solo para esta opcion...
                    echo "<td><a href='Inscripcion.php?curso=" . ($curso['codigo']) . "'><button>Inscribirse</button></a></td>";
                } else {
                    echo "<td><a href='registro.php'><button>Inscribirse</button></a></td>";
                }
            }

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No hay cursos disponibles</td></tr>";
    }
    echo "</table>";
}

function mostrarOpcionesAdministrador()
{
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<a href='activarDesactivar.php'><button>Activar/Desactivar Cursos</button></a>";
    echo "<a href='baremacionCursos.php'><button>Baremación Automática</button></a>";
    echo "<a href='listarAdmitidos.php'><button>Listado de Admitidos</button></a>";
    echo "<a href='insertaEliminaCurso.php'><button>Incorporar/Eliminar Cursos</button></a>";
    echo "</div>";
}

function mostrarMensaje(){
    if (isset($_SESSION['mensaje'])) {
        echo "<h2 style='text-align:center; color:green;'> ".$_SESSION['mensaje']."</h2>";
        $_SESSION['mensaje'] = ' ';
    } 
    echo "<h2 '></h2>";
}