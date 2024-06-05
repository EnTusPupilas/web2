<?php

session_start();
// Obtener el número de identificación enviado desde el formulario
$id_user = $_POST['id_user'];

// Crear conexión
$host = "postgres1";
$port = "5432";
$dbname = "dbbancolombia";
$user = "postgres";
$password = "david";

// Cadena de conexión
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Conectar a PostgreSQL
$dbconn = pg_connect($conn_string);

// Verificar la conexión
if (!$dbconn) {
    die("Error: No se pudo conectar a la base de datos.\n");
}

// Validar que id_user no esté vacío y sea un número entero
if (empty($id_user) || !is_numeric($id_user) || intval($id_user) != $id_user) {
    die("Error: ID de usuario no válido.\n");
}

// Escapar la entrada del usuario
$id_user = pg_escape_string($dbconn, $id_user);

// Consultar si el número de identificación existe en la base de datos
$query = "SELECT * FROM users WHERE id_user = '$id_user'";
$result = pg_query($dbconn, $query);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . pg_last_error($dbconn));
}

// Verificar si se encontraron resultados
if (pg_num_rows($result) > 0) {
    // El número de identificación es válido, redirigir a options.html
    $row = pg_fetch_assoc($result);
    $_SESSION['id_user'] = $row['id_user'];
    header("Location: options.html");
    exit();
} else {
    // El número de identificación no es válido, redirigir a registration.html
    header("Location: registration.html");
    exit();
}

?>

