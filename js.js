// Función para obtener el nombre del usuario desde el servidor
function fetchUserName() {
    fetch('options.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('name').innerHTML = data.name;
            } else {
                console.error('Error al obtener el nombre del usuario:', data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });
}

// Llamar a la función para obtener el nombre del usuario al cargar la página
window.onload = fetchUserName;

// Función para dibujar un número en un lienzo (canvas).
function drawNumber(number) {
    var canvas = document.createElement('canvas'); // Crear un nuevo elemento canvas
    canvas.width = 100; // Ancho del lienzo
    canvas.height = 100; // Alto del lienzo
    var ctx = canvas.getContext('2d'); // Obtener el contexto 2D del lienzo

    // Estilo del texto
    ctx.font = '40px Arial'; // Configurar la fuente del texto
    ctx.fillStyle = 'black'; // Configurar el color del texto
    ctx.textAlign = 'center'; // Alinear el texto al centro
    ctx.textBaseline = 'middle'; // Alinear la línea base del texto al centro

    // Dibujar el número en el lienzo
    ctx.fillText(number, canvas.width / 2, canvas.height / 2);

    // Convertir el lienzo a una imagen y devolverla
    var imageURL = canvas.toDataURL(); // Obtener la URL de la imagen del lienzo
    return 'url(' + imageURL + ')'; // Devolver la URL de la imagen en formato CSS
}

// Funciones para validar la elección del usuario.
function validateProcedures() {
    validateChoice("procedures");
}

function validateDocuments() {
    validateChoice("documents");
}

function validateCashier() {
    validateChoice("cashier");
}

function validateConsultant() {
    validateChoice("consultant");
}

// Función para comprobar si una imagen existe en una URL.
function imageExists(url, callback) {
    var img = new Image(); // Crear un nuevo objeto Image
    img.onload = function () {
        callback(true); // Llamar al callback con true si la imagen se carga correctamente
    };
    img.onerror = function () {
        callback(false); // Llamar al callback con false si hay un error al cargar la imagen
    };
    img.src = url; // Configurar la fuente de la imagen
}

// Objeto que traduce las etiquetas para las opciones de la asignación.
var labels = {
    "procedures": "Trámites generales",
    "documents": "Solicitar documentos",
    "cashier": "Transacciones en caja",
    "consultant": "Asesorías"
};

// Objeto que traduce las etiquetas para las opciones de la asignación.
var labels2 = {
    "procedures": "A",
    "documents": "B",
    "cashier": "C",
    "consultant": "D"
};

// Función para validar la elección del usuario y realizar la asignación.
function validateChoice(userChoice) {
    var code = labels2[userChoice]; // Obtener el código correspondiente a la elección del usuario

    var xhr = new XMLHttpRequest(); // Crear un nuevo objeto XMLHttpRequest
    xhr.open("POST", "options.php", true); // Configurar la solicitud POST a options.php
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Configurar el encabezado de la solicitud

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText); // Parsear la respuesta JSON
                if (response.status === "success") {
                    var imageUrlJpg = userChoice + '.jpg';
                    var imageUrlPng = userChoice + '.png';

                    imageExists(imageUrlJpg, function (exists) {
                        if (exists) {
                            setBackgroundImage(imageUrlJpg, response, userChoice);
                        } else {
                            imageExists(imageUrlPng, function (exists) {
                                if (exists) {
                                    setBackgroundImage(imageUrlPng, response, userChoice);
                                } else {
                                    console.error('Imagen no encontrada en formatos JPG o PNG');
                                    document.getElementById('result').innerHTML = 'Imagen no encontrada.';
                                }
                            });
                        }
                    });
                } else {
                    document.getElementById('result').innerHTML = response.message; // Mostrar mensaje de error
                }
            } catch (e) {
                console.error("Error al parsear la respuesta JSON: ", e);
                document.getElementById('result').innerHTML = "Error en la respuesta del servidor."; // Mostrar mensaje de error
            }
        }
    };

    xhr.send("code=" + code); // Enviar la solicitud con el código del turno
}

// Función para establecer la imagen de fondo y actualizar la interfaz de usuario
function setBackgroundImage(imageUrl, response, userChoice) {
    document.getElementById('btn_user').style.backgroundImage = 'url("' + imageUrl + '")'; // Establecer la imagen de fondo del botón del usuario
    document.getElementById('btn_user').style.backgroundSize = 'cover'; // Configurar el tamaño de la imagen de fondo
    document.getElementById('btn_user').style.backgroundPosition = 'center center'; // Configurar la posición de la imagen de fondo
    document.getElementById('lbl_user').innerHTML = labels[userChoice]; // Actualizar la etiqueta del usuario

    document.getElementById('btn_cpu').style.backgroundImage = drawNumber(response.message); // Establecer la imagen de fondo del botón de la CPU
    document.getElementById('btn_cpu').style.backgroundSize = 'cover'; // Configurar el tamaño de la imagen de fondo
    document.getElementById('btn_cpu').style.backgroundPosition = 'center center'; // Configurar la posición de la imagen de fondo
    document.getElementById('lbl_cpu').innerHTML = response.message; // Actualizar la etiqueta de la CPU

    document.getElementById('result').innerHTML = "Turno asignado: " + response.message; // Mostrar el resultado del turno asignado
}

// Función para manejar la respuesta de la solicitud fetch
function handleResponse(response) {
    try {
        const data = JSON.parse(response); // Parsear la respuesta JSON
        if (data.status === 'success') {
            console.log(data.message); // Manejar la respuesta exitosa
        } else {
            console.error(data.message); // Manejar el error
        }
    } catch (error) {
        console.error('Error al parsear la respuesta JSON:', error);
        console.error('Respuesta recibida:', response); // Mostrar el error
    }
}

// Ejemplo de uso de fetch para obtener el nombre del usuario
fetch('options.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('name').innerHTML = data.name;
        } else {
            console.error('Error al obtener el nombre del usuario:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

// Ejemplo de uso
fetch('options.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
        'code': 'ABC123' // Reemplaza con los datos correctos
    })
})
    .then(response => response.text())
    .then(handleResponse)
    .catch(error => console.error('Error en la solicitud:', error));
