<?php
// Configuración del encabezado HTTP para que el contenido sea JSON
header('Content-Type: application/json');

// Configuración manual de cookies de sesión con SameSite
if (PHP_VERSION_ID < 70300) {
    // Configuración de cookies para versiones de PHP menores a 7.3.0
    session_set_cookie_params(0, '/', '', false, true);
    // Iniciar la sesión
    session_start();
    // Si existe la cookie 'PHPSESSID', establecerla nuevamente para mantener la sesión 
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], 0, '/', '', false, true);
    }
} else {
    // Configuración de cookies para versiones de PHP 7.3.0 o mayores
    session_set_cookie_params([
        'lifetime' => 0,            // La cookie de sesión dura hasta que se cierra el navegador
        'path' => '/',              // La cookie es válida en toda la aplicación
        'domain' => '',             // Ajustar si se usa un dominio específico
        'secure' => false,          // Cambiar a true si se usa HTTPS
        'httponly' => true,         // Solo accesible a través del protocolo HTTP (no JavaScript)
        'samesite' => 'Lax'         // Política SameSite (cambiar a 'None' para permitir contextos de terceros)
    ]);
    session_start();
}

// Datos de conexión a la base de datos PostgreSQL
$host = "postgres1";                // Host de la base de datos
$port = "5432";                     // Puerto de la base de datos
$dbname = "dbbancolombia";          // Nombre de la base de datos
$user = "postgres";                 // Usuario de la base de datos
$password = "david";                // Contraseña del usuario de la base de datos

// Cadena de conexión a PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Conectar a PostgreSQL
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    // Si la conexión falla, se retorna un mensaje de error en formato JSON y se detiene el script
    echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos']);
    exit;
}

// Obtener el ID del usuario desde la sesión
$id_user = $_SESSION['id_user'] ?? null;
// Obtener el código enviado en la solicitud POST
$code = $_POST['code'] ?? '';

if (!$id_user) {
    // Si el usuario no está autenticado, se retorna un mensaje de error y se detiene el script
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

if (!$code) {
    // Si no se proporciona el código, se retorna un mensaje de error y se detiene el script
    echo json_encode(['status' => 'error', 'message' => 'Código no proporcionado']);
    exit;
}

// Comprobar si el usuario ya tiene un turno asignado
$query = "SELECT * FROM appointment WHERE id_user = $id_user";
$result = pg_query($dbconn, $query);

if (pg_num_rows($result) > 0) {
    // Si el usuario ya tiene un turno asignado, se retorna un mensaje informativo
    echo json_encode(['status' => 'exists', 'message' => 'Ya tienes un turno asignado.']);
    exit;
}

// Obtener el último turno asignado para el tipo de turno especificado por el código
$query = "SELECT appointment_description FROM appointment WHERE code = '$code' ORDER BY appointment_id DESC LIMIT 1";
$result = pg_query($dbconn, $query);
$last_turn = pg_fetch_assoc($result);

$new_turn_number = 1;
if ($last_turn) {
    // Si ya existe un turno previo, se incrementa el número del turno
    $new_turn_number = intval(substr($last_turn['appointment_description'], 1)) + 1;
}

// Crear la descripción del nuevo turno
$appointment_description = $code . $new_turn_number;

// Asignar el nuevo turno en la base de datos
$query = "INSERT INTO appointment (id_user, code, appointment_description, date, time) 
          VALUES ($id_user, '$code', '$appointment_description', CURRENT_DATE, CURRENT_TIME)";
$result = pg_query($dbconn, $query);

if ($result) {
    // Si la inserción es exitosa, se retorna el turno asignado
    echo json_encode(['status' => 'success', 'message' => $appointment_description]);
} else {
    // Si la inserción falla, se retorna un mensaje de error
    echo json_encode(['status' => 'error', 'message' => 'Error al asignar el turno.']);
}
?>
