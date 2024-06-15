<?php
session_start();
header('Content-Type: application/json');

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
    echo json_encode(['status' => 'error', 'message' => 'Error: No se pudo conectar a la base de datos.']);
    exit();
}

// Obtener el id_advisor de la sesión
$id_advisor = $_SESSION['id_advisor'] ?? null;

if (!$id_advisor) {
    echo json_encode(['status' => 'error', 'message' => 'Error: Sesión no iniciada.']);
    exit();
}

// Escapar el id_advisor para evitar inyección SQL
$id_advisor = pg_escape_string($dbconn, $id_advisor);

// Comenzar una transacción
pg_query($dbconn, "BEGIN");

// Actualizar la tabla appointment
$query_appointment = "UPDATE appointment SET attended = true WHERE id_advisor = $1 AND attended = false RETURNING id_user";
$result_appointment = pg_prepare($dbconn, "update_appointment", $query_appointment);
$result_appointment = pg_execute($dbconn, "update_appointment", array($id_advisor));

if (!$result_appointment) {
    pg_query($dbconn, "ROLLBACK");
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar appointment: ' . pg_last_error($dbconn)]);
    exit();
}

$id_user = pg_fetch_result($result_appointment, 0, 'id_user');

// Actualizar la tabla users
$query_users = "UPDATE users SET released_user = true WHERE id_user = $1";
$result_users = pg_prepare($dbconn, "update_users", $query_users);
$result_users = pg_execute($dbconn, "update_users", array($id_user));

if (!$result_users) {
    pg_query($dbconn, "ROLLBACK");
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar users: ' . pg_last_error($dbconn)]);
    exit();
}

// Actualizar la tabla advisors
$query_advisors = "UPDATE advisors SET released_advisor = true WHERE id_advisor = $1";
$result_advisors = pg_prepare($dbconn, "update_advisors", $query_advisors);
$result_advisors = pg_execute($dbconn, "update_advisors", array($id_advisor));

if (!$result_advisors) {
    pg_query($dbconn, "ROLLBACK");
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar advisors: ' . pg_last_error($dbconn)]);
    exit();
}

// Confirmar la transacción
pg_query($dbconn, "COMMIT");

echo json_encode(['status' => 'success', 'message' => 'Actualización realizada con éxito.']);

// Cerrar la conexión
pg_close($dbconn);
?>
