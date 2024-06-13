function checkAndAssignAdvisor() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "assign_advisor.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = JSON.parse(xhr.responseText);
            console.log(response.message);
            // Aquí puedes actualizar la interfaz del usuario con la respuesta
        }
    };
    xhr.send();
}

// Verificar y asignar asesor cada 10 segundos
setInterval(checkAndAssignAdvisor, 10000);
