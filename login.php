<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AFFIJO2025</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 1rem;
        }

        .login-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 3rem 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo-img {
            width: 35%;
            height: auto;
            display: block;
            margin-bottom: 1rem;
        }

        h2 {
            color: var(--text-color);
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-top: 0.5rem;
        }

        .input-group i.fa-user,
        .input-group i.fa-lock {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 2.5rem;
            border: 1px solid #ccc;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-color: var(--accent-color);
            outline: none;
        }

        .toggle-password-outer {
            width: 100%;
            text-align: right;
            margin-top: 0.25rem;
        }

        .toggle-password-outer button {
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
            font-size: 0.9rem;
        }

        .submit-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem;
            width: 100%;
            font-size: 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background: #2980b9;
        }

        p {
            margin-top: 1rem;
            color: #888;
            font-size: 0.9rem;
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 2rem 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Imagen de logo -->
        <img src="logo.png" class="logo-img" alt="Logo de AFFIJO2025">

        <!-- Formulario -->
        <h2>Control de material JMAS</h2>
        <form action="validar_login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="usuario" placeholder="Usuario" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="clave" id="clave" placeholder="Contraseña" required>
            </div>
            <!-- Botón fuera del campo -->
            <div class="toggle-password-outer">
                <button type="button" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggle-icon"></i> Mostrar contraseña
                </button>
            </div>
            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        <p><i class="fas fa-question-circle"></i> ¿No cuentas con usuario? Regístrate</p>
        <button type="submit" class="submit-btn">
            <i class="fas fa-user-plus"></i> Regístrarme
        </button>
    </div>


    <!--Boton para mostrar/ocultar la contraseña-->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("clave");
            const icon = document.getElementById("toggle-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
