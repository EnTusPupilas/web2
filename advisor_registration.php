<?php

session_start();
// Obtener los datos enviados desde el formulario
$id_advisor = $_POST['id_advisor'];
$advisor_name = $_POST['advisor_name'];
$advisor_lastname = $_POST['advisor_lastname'];
$pw_advisor = $_POST['pw_advisor'];

// Datos de conexión
$host = "postgres1";
$port = "5432";
$dbname = "dbbancolombia";
$user = "postgres";
$password = "david";

// Cadena de conexión
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Conectar a PostgreSQL
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    // Manejo de error de conexión
    echo "Error: No se pudo conectar a la base de datos.";
    exit();
}

// Cifrar la contraseña
$hashed_password = password_hash($pw_advisor, PASSWORD_DEFAULT);

// Inserta los valores de manera segura usando parámetros preparados
$query = "INSERT INTO advisors (id_advisor, advisor_name, advisor_lastname, pw_advisor) VALUES ($1, $2, $3, $4)";
$result = pg_query_params($dbconn, $query, array($id_advisor, $advisor_name, $advisor_lastname, $hashed_password));

if ($result) {
    // La inserción fue exitosa, redirigir a advisor_interface.html
    $_SESSION['id_advisor'] = $id_advisor;
    header("Location: advisor_interface.html");
    exit();
} else {
    // Hubo un error en la inserción, redirigir a advisor_registration.html
    // Opcionalmente, puedes imprimir el error para debugging
    echo "Error en la consulta: " . pg_last_error($dbconn);
    header("Location: advisor_registration.html");
    exit();
}

// Cerrar la conexión
pg_close($dbconn); 

?>
