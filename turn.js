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

function setBackgroundImagePrev(appointmentDescriptionPrev) {
    var button = document.getElementById('btn_procedures_turn_prev');
    button.style.backgroundImage = drawNumber(appointmentDescriptionPrev); // Establecer la imagen de fondo del botón turno
    button.style.backgroundSize = 'cover'; // Configurar el tamaño de la imagen de fondo
    button.style.backgroundPosition = 'center center'; // Configurar la posición de la imagen de fondo
}

// Función para actualizar la interfaz de usuario
function updateUI(response) {
    if (response.result) {
        document.getElementById('result').innerText = response.result;
    } else {
        if (response.btn_procedures_turn) {
            setBackgroundImage(response.btn_procedures_turn);
            document.getElementById('module').innerText = response.module;
            document.getElementById('advisor_name').innerText = response.advisor_name;
            document.getElementById('advisor_lastname').innerText = response.advisor_lastname;
        }
        
        if (response.btn_procedures_turn_prev) {
            setBackgroundImagePrev(response.btn_procedures_turn_prev);
            document.getElementById('module_prev').innerText = response.module_prev;
            document.getElementById('advisor_name_prev').innerText = response.advisor_name_prev;
            document.getElementById('advisor_lastname_prev').innerText = response.advisor_lastname_prev;
        }
    }
}

// Función para realizar la llamada AJAX y actualizar la UI
function fetchAndUpdateUI() {
    fetch('turn.php')
        .then(response => response.json())
        .then(data => updateUI(data))
        .catch(error => console.error('Error:', error));
}

// Llamar a la función inicialmente para actualizar la UI inmediatamente
fetchAndUpdateUI();

// Verificar turnos cada 60 segundos
setInterval(fetchAndUpdateUI, 60000);
