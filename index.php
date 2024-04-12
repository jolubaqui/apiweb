<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "api";


$conexion = new mysqli($host, $user, $password, $db);

if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}

header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];


$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarId = explode('/', $path);
$id = ($path !== '/') ? end($buscarId) : null;



switch ($metodo) {

        //SELECT usuarios
    case 'GET':
        consultar($conexion, $id);
        break;

        //INSERT usuarios
    case 'POST':
        insertar($conexion);
        break;

        //UPDATE usuarios        
    case 'PUT':
        actualizar($conexion, $id);
        break;

        //DELETE usuarios
    case 'DELETE':
        borrar($conexion, $id);
        break;

    default:
        echo 'Método no permitido';
        break;
}

function consultar($conexion, $id)
{

    $sql = ($id === null) ? "SELECT *FROM usuarios" : "SELECT *FROM usuarios WHERE ID=$id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
};

function insertar($conexion)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuarios (nombre) values ('$nombre')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $dato["id"] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        echo json_encode(array('error' => 'Error a crear usuario'));
    }
}

function borrar($conexion, $id)
{
    echo "El id a borrar es: " . $id;

    $sql = "DELETE FROM  usuarios WHERE ID=$id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario borrado'));
    } else {
        echo json_encode(array('error' => 'Error al borrar usuario'));
    }
}

function actualizar($conexion, $id)
{

    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE ID=$id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario actualizado'));
    } else {
        echo json_encode(array('error' => 'Error al actualizar usuario'));
    }
}
