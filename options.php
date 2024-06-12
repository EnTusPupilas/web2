<?php
// Configuración del encabezado HTTP para que el contenido sea JSON
header('Content-Type: application/json');

// Configuración manual de cookies de sesión con SameSite
if (PHP_VERSION_ID < 70300) {
    session_set_cookie_params(0, '/', '', false, true);
    session_start();
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], 0, '/', '', false, true);
    }
} else {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Datos de conexión a la base de datos PostgreSQL
$host = "postgres1";
$port = "5432";
$dbname = "dbbancolombia";
$user = "postgres";
$password = "david";

// Cadena de conexión a PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Conectar a PostgreSQL
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos']);
    exit;
}

// Obtener el ID del usuario desde la sesión
$id_user = $_SESSION['id_user'] ?? null;

if (!$id_user) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener el nombre del usuario
$query = "SELECT name FROM users WHERE id_user = $id_user";
$result = pg_query($dbconn, $query);
$user_data = pg_fetch_assoc($result);

if (!$user_data) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    exit;
}

$user_name = $user_data['name'];

// Si se recibe un código POST, manejar la asignación de turnos
$code = $_POST['code'] ?? null;

if ($code) {
    // Comprobar si el usuario ya tiene un turno asignado
    $query = "SELECT * FROM appointment WHERE id_user = $id_user";
    //$query = "SELECT released_user FROM users WHERE id_user = $id_user";
    $result = pg_query($dbconn, $query);

    if (pg_num_rows($result) > 0) {
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
        echo json_encode(['status' => 'success', 'message' => $appointment_description, 'name' => $user_name]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al asignar el turno.']);
    }
} else {
    // Si no se proporciona un código, solo retornar el nombre del usuario
    echo json_encode(['status' => 'success', 'name' => $user_name]);
}
?>
