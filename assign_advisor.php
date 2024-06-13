<?php
// Conectar a la base de datos
$host = "postgres1";
$port = "5432";
$dbname = "dbbancolombia";
$user = "postgres";
$password = "david";

// Crear la conexión
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . pg_last_error()]));
}

// Verificar si hay algún asesor disponible
$sql = "SELECT id_advisor FROM advisors WHERE released_advisor = 't' LIMIT 1";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) > 0) {
    // Obtener el ID del asesor disponible
    $row = pg_fetch_assoc($result);
    $id_advisor = $row['id_advisor'];

    // Buscar una cita no atendida y sin asesor asignado
    $sql = "SELECT appointment_id FROM appointment WHERE attended = 'f' AND id_advisor IS NULL LIMIT 1";
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        // Obtener el ID de la cita encontrada
        $row = pg_fetch_assoc($result);
        $appointment_id = $row['appointment_id'];

        // Iniciar una transacción
        pg_query($conn, "BEGIN");

        // Asignar el asesor a la cita
        $sql = "UPDATE appointment SET id_advisor = $id_advisor WHERE appointment_id = $appointment_id";
        if (pg_query($conn, $sql)) {
            // Marcar el asesor como ocupado
            $sql = "UPDATE advisors SET released_advisor = 'f' WHERE id_advisor = $id_advisor";
            if (pg_query($conn, $sql)) {
                // Confirmar la transacción
                pg_query($conn, "COMMIT");
                echo json_encode(["success" => true, "message" => "Asesor asignado exitosamente"]);
            } else {
                // Revertir la transacción
                pg_query($conn, "ROLLBACK");
                echo json_encode(["success" => false, "message" => "Error al marcar asesor como ocupado"]);
            }
        } else {
            // Revertir la transacción
            pg_query($conn, "ROLLBACK");
            echo json_encode(["success" => false, "message" => "Error al asignar asesor a la cita"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No hay citas sin atender"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No hay asesores disponibles"]);
}

// Cerrar la conexión
pg_close($conn);
?>
