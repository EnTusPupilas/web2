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
    echo json_encode(["result" => "Error al conectar a la base de datos."]);
    exit;
}

// Consulta para obtener los dos turnos más antiguos sin atender
$query = "
    SELECT 
        a.appointment_description, 
        adv.module, 
        adv.advisor_name, 
        adv.advisor_lastname
    FROM 
        appointment a
    JOIN 
        advisors adv ON a.id_advisor = adv.id_advisor
    WHERE 
        a.attended = FALSE
    ORDER BY 
        a.appointment_id
    LIMIT 2;
";

$result = pg_query($dbconn, $query);

if (!$result) {
    echo json_encode(["result" => "Error en la consulta."]);
    exit;
}

$appointments = pg_fetch_all($result);

if (!$appointments) {
    echo json_encode(["result" => "No hay turnos por atender."]);
    exit;
}

$response = [];

if (count($appointments) >= 2) {
    $response['btn_procedures_turn'] = $appointments[1]['appointment_description'];
    $response['module'] = $appointments[1]['module'];
    $response['advisor_name'] = $appointments[1]['advisor_name'];
    $response['advisor_lastname'] = $appointments[1]['advisor_lastname'];
    
    $response['btn_procedures_turn_prev'] = $appointments[0]['appointment_description'];
    $response['module_prev'] = $appointments[0]['module'];
    $response['advisor_name_prev'] = $appointments[0]['advisor_name'];
    $response['advisor_lastname_prev'] = $appointments[0]['advisor_lastname'];
} else if (count($appointments) == 1) {
    $response['btn_procedures_turn'] = $appointments[0]['appointment_description'];
    $response['module'] = $appointments[0]['module'];
    $response['advisor_name'] = $appointments[0]['advisor_name'];
    $response['advisor_lastname'] = $appointments[0]['advisor_lastname'];
}

echo json_encode($response);

pg_close($dbconn);
?>
