<?php

session_start();
// Obtener los datos enviados desde el formulario
$id_user = $_POST['id_user'];
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$phone_number = $_POST['phone_number'];

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

// Inserta los valores de manera segura usando parámetros preparados
$query = "INSERT INTO users (id_user, name, lastname, phone_number) VALUES ($1, $2, $3, $4)";
$result = pg_query_params($dbconn, $query, array($id_user, $name, $lastname, $phone_number));

if ($result) {
    // La inserción fue exitosa, redirigir a options.html
    $_SESSION['id_user'] = $id_user;
    header("Location: options.html");
    exit();
} else {
    // Hubo un error en la inserción, redirigir a registration.html
    // Opcionalmente, puedes imprimir el error para debugging
    echo "Error en la consulta: " . pg_last_error($dbconn);
    header("Location: registration.html");
    exit();
}

?>
