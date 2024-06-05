<?php
// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Configuración manual de cookies de sesión con SameSite
if (PHP_VERSION_ID < 70300) {
    // Establecer los parámetros de la cookie de sesión
    session_set_cookie_params(0, '/', '', false, true);
    // Iniciar la sesión
    session_start();
    // Si existe la cookie 'PHPSESSID', establecerla nuevamente para mantener la sesión
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], 0, '/', '', false, true);
    }
} else {
    // Establecer los parámetros de la cookie de sesión usando un array
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '', // Ajusta esto si usas un dominio específico
        'secure' => false, // Cambiar a true si usas HTTPS
        'httponly' => true,
        'samesite' => 'Lax' // Cambia esto a 'None' si necesitas que la cookie esté disponible en contextos de terceros
    ]);
    // Iniciar la sesión
    session_start();
}

// Datos de conexión a la base de datos PostgreSQL
$host = "postgres1";
$port = "5432";
$dbname = "dbbancolombia";
$user = "postgres";
$password = "david";

// Cadena de conexión a la base de datos PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Conectar a PostgreSQL
$dbconn = pg_connect($conn_string);

// Verificar si la conexión a la base de datos fue exitosa
if (!$dbconn) {
    echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos']);
    exit;
}

// Obtener el ID de usuario de la sesión actual o establecerlo como nulo si no está definido
$id_user = $_SESSION['id_user'] ?? null;
// Obtener el código del turno proporcionado por la solicitud POST o establecerlo como una cadena vacía si no está definido
$code = $_POST['code'] ?? '';

// Verificar si el usuario está autenticado
if (!$id_user) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se proporcionó un código de turno
if (!$code) {
    echo json_encode(['status' => 'error', 'message' => 'Código no proporcionado']);
    exit;
}

// Comprobar si el usuario ya tiene un turno asignado
$query = "SELECT * FROM appointment WHERE id_user = $id_user";
$result = pg_query($dbconn, $query);

// Si el usuario ya tiene un turno asignado, devolver un mensaje de error
if (pg_num_rows($result) > 0) {
    echo json_encode(['status' => 'exists', 'message' => 'Ya tienes un turno asignado.']);
    exit;
}

// Obtener el último turno asignado para el tipo seleccionado
$query = "SELECT appointment_description FROM appointment WHERE code = '$code' ORDER BY appointment_id DESC LIMIT 1";
$result = pg_query($dbconn, $query);
$last_turn = pg_fetch_assoc($result);

// Generar el número del nuevo turno
$new_turn_number = 1;
if ($last_turn) {
    $new_turn_number = intval(substr($last_turn['appointment_description'], 1)) + 1;
}
$appointment_description = $code . $new_turn_number;

// Asignar el nuevo turno
$query = "INSERT INTO appointment (id_user, code, appointment_description, date, time) 
          VALUES ($id_user, '$code', '$appointment_description', CURRENT_DATE, CURRENT_TIME)";
$result = pg_query($dbconn, $query);

// Verificar si la asignación del turno fue exitosa y devolver una respuesta JSON apropiada
if ($result) {
    echo json_encode(['status' => 'success', 'message' => $appointment_description]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al asignar el turno.']);
}
?>
