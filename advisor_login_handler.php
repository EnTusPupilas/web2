<?php

session_start();
// Obtener el número de identificación enviado desde el formulario
$id_advisor = $_POST['id_advisor'];
$pw_advisor = $_POST['pw_advisor'];


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

// Validar que id_advisor no esté vacío y sea un número entero
if (empty($id_advisor) || !is_numeric($id_advisor) || intval($id_advisor) != $id_advisor) {
    die("Error: ID de usuario no válido.\n");
}

// Escapar la entrada del usuario
$id_advisor = pg_escape_string($dbconn, $id_advisor);

// Preparar y ejecutar la consulta para validar el id_advisor
$query = 'SELECT * FROM advisors WHERE id_advisor = $1';
$result = pg_prepare($dbconn, "my_query", $query);
$result = pg_execute($dbconn, "my_query", array($id_advisor));

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . pg_last_error($dbconn));
}

// Verificar si se encontraron resultados
if (pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);

    // Verificar la contraseña usando password_verify
    if (password_verify($pw_advisor, $row['pw_advisor'])) {
        // El número de identificación y la contraseña son válidos, redirigir a advisor_interface.html
        $_SESSION['id_advisor'] = $row['id_advisor'];
        header("Location: advisor_interface.html");
        exit();
    } else {
        // Contraseña incorrecta
        $_SESSION['error_message'] = "Contraseña incorrecta";
        header("Location: advisor_login.php");
        exit();
    }
} else {
    // El número de identificación no es válido
    header("Location: advisor_registration.html");
    exit();
}

// Cerrar la conexión
pg_close($dbconn); 
?>