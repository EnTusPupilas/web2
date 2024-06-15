// Función para obtener el nombre del usuario desde el servidor
function fetchAdvisorName() {
    fetch('advisor_interface.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('advisor_name').innerHTML = data.advisor_name;
                document.getElementById('name').innerHTML = data.name;
                document.getElementById('lastname').innerHTML = data.lastname;
                setBackgroundImage(data.appointment_description);
                document.getElementById('lbl_procedures_turn').innerHTML = data.appointment_description;
            } else {
                console.error('Error al obtener el nombre del usuario:', data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });
}

// Llamar a la función para obtener el nombre del usuario al cargar la página
window.onload = fetchAdvisorName;

// Función para dibujar un número en un lienzo (canvas)
function drawNumber(number) {
    var canvas = document.createElement('canvas'); // Crear un nuevo elemento canvas
    canvas.width = 100; // Ancho del lienzo
    canvas.height = 100; // Alto del lienzo
    var ctx = canvas.getContext('2d'); // Obtener el contexto 2D del lienzo

    // Estilo del texto
    ctx.font = '40px Arial'; // Configurar la fuente del texto
    ctx.fillStyle = 'white'; // Configurar el color del texto
    ctx.textAlign = 'center'; // Alinear el texto al centro
    ctx.textBaseline = 'middle'; // Alinear la línea base del texto al centro

    // Dibujar el número en el lienzo
    ctx.fillText(number, canvas.width / 2, canvas.height / 2);

    // Convertir el lienzo a una imagen y devolverla
    var imageURL = canvas.toDataURL(); // Obtener la URL de la imagen del lienzo
    return 'url(' + imageURL + ')'; // Devolver la URL de la imagen en formato CSS
}

// Función para establecer la imagen de fondo y actualizar la interfaz de usuario
function setBackgroundImage(appointmentDescription) {
    var button = document.getElementById('btn_procedures_turn');
    button.style.backgroundImage = drawNumber(appointmentDescription); // Establecer la imagen de fondo del botón turno
    button.style.backgroundSize = 'cover'; // Configurar el tamaño de la imagen de fondo
    button.style.backgroundPosition = 'center center'; // Configurar la posición de la imagen de fondo
}

function finalValidation() {
    fetch('final_validation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            // Puedes pasar datos adicionales si es necesario
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Actualizar la interfaz de usuario si es necesario
            console.log('Validación finalizada con éxito');
        } else {
            console.error('Error en la validación final:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
}
