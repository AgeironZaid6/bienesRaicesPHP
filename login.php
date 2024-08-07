<?php

//importar la conexion a la base de datos
require 'includes/config/database.php';
$db = conectarBD();


// Inicializar las variables de alerta y formulario activo
$alerta = "";
$form = isset($_POST['action']) ? $_POST['action'] : 'login';

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form === 'login') {
        // Proceso de inicio de sesión
        $email = $_POST['login-email'];
        $password = $_POST['login-password'];

        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user) {
                session_start();
                $_SESSION['usuario'] = $user['email'];
                $_SESSION['login'] = true;
                $_SESSION['nivel'] = $user['nivel'];

                switch ($user['nivel']) {
                    case 1:
                        header("Location: /index.php");
                        break;
                    case 2:
                        header("Location: /admin");
                        break;
                    default:
                        header("Location: /");
                        break;
                }

                header("Location: /index.php");
            }
        } else {
            $alerta = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Credenciales incorrectas',
                        text: 'El correo electrónico o la contraseña son incorrectos.',
                    });
                });
            </script>";
        }
    } elseif ($form === 'register') {
        // Proceso de registro
        $nombre = $_POST['register-name'];
        $email = $_POST['register-email'];
        $password = $_POST['register-password'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sss', $email, $hashed_password, $nombre);
        $result = $stmt->execute();

        if ($result) {
            $alerta = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro exitoso',
                        text: 'Puedes iniciar sesión con tus credenciales.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            </script>";
        } else {
            $alerta = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar',
                        text: 'Hubo un problema al registrar tu cuenta.',
                    });
                });
            </script>";
        }
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .background {
            position: relative;
            height: 100vh;
            overflow: hidden;
            background: url('./src/img/loginimg.jpg') no-repeat center center fixed;
            background-size: cover;
            animation: zoomIn 20s infinite alternate;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .form-container {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .form-box {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .form-box h2 {
            margin-bottom: 20px;
            color: #343a40;
        }

        .form-box .form-control {
            border-radius: 5px;
        }

        .form-box .btn {
            border-radius: 5px;
        }

        .form-box a {
            color: #007bff;
            text-decoration: none;
        }

        .form-box a:hover {
            text-decoration: underline;
        }

        .toggle-link {
            cursor: pointer;
        }

        @keyframes zoomIn {
            from {
                background-size: 110%;
            }

            to {
                background-size: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="background">
        <div class="overlay"></div>
        <div class="form-container">
            <!-- Login Form -->
            <div id="login-form" class="form-box">
                <h2 class="text-center">Iniciar Sesión</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label for="login-email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="login-email" name="login-email"
                            placeholder="Ingresa tu correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="login-password" name="login-password"
                            placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </div>
                    <p class="text-center mt-3"><a href="#" class="toggle-link" onclick="toggleForms()">¿No tienes una
                            cuenta? Regístrate</a></p>
                </form>
            </div>
            <!-- Register Form -->
            <div id="register-form" class="form-box" style="display: none;">
                <h2 class="text-center">Registrarse</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label for="register-name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="register-name" name="register-name"
                            placeholder="Ingresa tu nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="register-email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="register-email" name="register-email"
                            placeholder="Ingresa tu correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="register-password" name="register-password"
                            placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="mb-3">
                        <a href="terminos_condiciones.php">Acepto Terminos y Condiciones</a>
                        <input type="checkbox" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </div>
                    <p class="text-center mt-3"><a href="#" class="toggle-link" onclick="toggleForms()">¿Ya tienes una
                            cuenta? Inicia sesión</a></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.2/js/bootstrap.min.js"></script>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }
    </script>

    <?php echo $alerta; ?>

    <?php
    incluirTemplate('footer');
    ?>