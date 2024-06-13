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

// Consultar los datos del asesor
$query = 'SELECT advisor_name FROM advisors WHERE id_advisor = $1';
$result = pg_prepare($dbconn, "advisor_query", $query);
$result = pg_execute($dbconn, "advisor_query", array($id_advisor));

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta: ' . pg_last_error($dbconn)]);
    exit();
}

if (pg_num_rows($result) > 0) {
    $advisor = pg_fetch_assoc($result);

    // Consultar los datos del usuario que aun no ha sido atendido
    $query_user = 'SELECT u.name, u.lastname, a.appointment_description 
                   FROM users u 
                   JOIN appointment a ON u.id_user = a.id_user 
                   WHERE a.id_advisor = $1 AND a.attended = false';
    $result_user = pg_prepare($dbconn, "user_query", $query_user);
    $result_user = pg_execute($dbconn, "user_query", array($id_advisor));

    if (!$result_user) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta de usuario: ' . pg_last_error($dbconn)]);
        exit();
    }

    if (pg_num_rows($result_user) > 0) {
        $user = pg_fetch_assoc($result_user);
        echo json_encode([
            'status' => 'success',
            'advisor_name' => $advisor['advisor_name'],
            'name' => $user['name'],
            'lastname' => $user['lastname'],
            'appointment_description' => $user['appointment_description'] 
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay citas pendientes para este asesor']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Asesor no encontrado.']);
}

// Cerrar la conexión
pg_close($dbconn);
?>
