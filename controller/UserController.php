<?php

class UserController extends BaseController
{
    private function logoutUser()
    {
        unset($_SESSION['current_user']);
    }

    private function generarCodigoValidacion(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generarTokenValidacion(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function construirUrlValidacion(string $email, string $token): string
    {
        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $baseUrl = rtrim($config['app_base_url'] ?? 'http://localhost/tpfinal_mvc', '/');
        $query = http_build_query([
            'controller' => 'User',
            'method' => 'validarCorreo',
            'email' => $email,
            'token' => $token,
        ]);

        return $baseUrl . '/index.php?' . $query;
    }

    private function enviarCodigoValidacion(string $email, string $codigo, string $token, string $nombre = ''): bool
    {
        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $asunto = 'Activa tu cuenta en Arcade de Desafíos';
        $urlValidacion = $this->construirUrlValidacion($email, $token);
        $nombre = trim($nombre);
        $saludo = $nombre !== '' ? 'Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',' : 'Hola,';
        $mensajeHtml = "<p>$saludo</p>"
            . "<p>Gracias por registrarte en <strong>Arcade de Desafíos</strong>.</p>"
            . "<p>Tu código de validación es: <strong>$codigo</strong></p>"
            . "<p>O bien, activa tu cuenta haciendo clic en el siguiente enlace:</p>"
            . "<p><a href=\"$urlValidacion\">Activar mi cuenta</a></p>"
            . "<p>Si no solicitaste este registro, puedes ignorar este mensaje.</p>";
        $mensajeTexto = "$saludo\n\nGracias por registrarte en Arcade de Desafíos.\nTu código de validación es: $codigo\n\nO bien, activa tu cuenta haciendo clic en este enlace:\n$urlValidacion\n\nSi no solicitaste este registro, puedes ignorar este mensaje.\n";

        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            if (!empty($config['smtp_host'])) {
                $mail->isSMTP();
                $mail->Host = $config['smtp_host'];
                $mail->Port = (int) ($config['smtp_port'] ?? 587);
                $mail->CharSet = 'UTF-8';

                if (!empty($config['smtp_username']) && !empty($config['smtp_password'])) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $config['smtp_username'];
                    $mail->Password = $config['smtp_password'];
                    $mail->SMTPSecure = $config['smtp_encryption'] ?? 'tls';
                } else {
                    $mail->SMTPAuth = false;
                    $mail->SMTPSecure = '';
                }
            } else {
                $mail->isMail();
            }

            $mail->setFrom($config['mail_from'] ?? 'no-reply@arcade.local', $config['mail_from_name'] ?? 'Arcade de Desafíos');
            $mail->addAddress($email);
            $mail->addReplyTo($config['mail_from'] ?? 'no-reply@arcade.local', $config['mail_from_name'] ?? 'Arcade de Desafíos');
            $mail->Subject = $asunto;
            $mail->Body = $mensajeHtml;
            $mail->AltBody = $mensajeTexto;
            $mail->isHTML(true);

            $mail->send();
            Log::info("Correo de validación enviado a $email");
            return true;
        } catch (Exception $e) {
            Log::warning('Error al enviar el correo real: ' . $e->getMessage());
            return false;
        }
    }



    public function comprarCoins()
    {
        $this->ensureSession();
        $email = $this->getCurrentUserEmail();
        if (!$email) {
            $this->jsonResponse(['success' => false, 'error' => 'Sesión inválida'], 400);
        }

        $added = $this->model->sumarMonedasUsuario($email, 10, 1);
        if (!$added) {
            $this->jsonResponse(['success' => false, 'error' => 'No se pudo completar la compra'], 500);
        }

        $coins = $this->model->obtenerMonedasUsuarioPorEmail($email);
        $this->jsonResponse(['success' => true, 'coins' => $coins]);
    }

    public function verPerfil()
    {
        $this->ensureSession();
        $userId = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$userId) {
            Redirect::to('/tpfinal_mvc/User/home');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $currentUserId = null;
        if (!empty($_SESSION['user_id'])) {
            $currentUserId = intval($_SESSION['user_id']);
        } elseif (!empty($currentUser['id'])) {
            $currentUserId = intval($currentUser['id']);
        }

        if ($currentUserId !== null && $currentUserId === $userId) {
            Redirect::to('/tpfinal_mvc/User/perfil');
            return;
        }

        $requestedUser = $this->model->obtenerUsuarioPorId($userId);
        if (!$requestedUser) {
            Redirect::to('/tpfinal_mvc/User/home');
            return;
        }

        $requestedUser['username'] = $requestedUser['usuario'] ?? $requestedUser['nombre'];
        $this->render('profileView', [
            'user' => $requestedUser,
            'readonly' => true
        ]);
    }

    public function home()
    {
        Log::info("GameController::home");
        $this->ensureSession();
        $user = $this->getCurrentUser();
        $this->render('homeView', [
            'title' => 'Arcade de Desafíos',
            'subtitle' => 'Regístrate, valida tu correo y únete al juego de preguntas',
            'score' => $user['score'] ?? 0,
        ]);
    }

    public function registro()
    {
        $this->ensureSession();
        $this->render('registerView', [
            'nombre' => '',
            'apellido' => '',
            'usuario' => '',
            'anio_nacimiento' => '',
            'sexo' => '',
            'ciudad' => '',
            'pais' => '',
            'email' => ''
        ]);
    }

    public function procesarRegistro()
    {
        $this->ensureSession();

        $nombre = trim($this->request->post('nombre'));
        $apellido = trim($this->request->post('apellido'));
        $usuario = trim($this->request->post('usuario'));
        $anio_nacimiento = trim($this->request->post('anio_nacimiento'));
        $sexo = trim($this->request->post('sexo'));
        $ciudad = trim($this->request->post('ciudad'));
        $pais = trim($this->request->post('pais'));
        $email = trim($this->request->post('email'));
        $password = trim($this->request->post('password'));
        $repetirPassword = trim($this->request->post('repetirPassword'));
        $foto_perfil = null;

        if (!empty($_FILES['foto_perfil']['name'])) {
            $uploadDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $tmpName = $_FILES['foto_perfil']['tmp_name'];
            $extension = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if ($_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
                $this->render('registerView', [
                    'error' => 'Error al subir la foto de perfil. Intenta de nuevo.',
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'usuario' => $usuario,
                    'anio_nacimiento' => $anio_nacimiento,
                    'sexo' => $sexo,
                    'isM' => $sexo === 'M',
                    'isF' => $sexo === 'F',
                    'isO' => $sexo === 'O',
                    'ciudad' => $ciudad,
                    'pais' => $pais,
                    'email' => $email,
                ]);
                return;
            }

            if (!in_array($extension, $allowedExtensions) || !getimagesize($tmpName)) {
                $this->render('registerView', [
                    'error' => 'Solo se aceptan imágenes válidas (jpg, png, gif, webp).',
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'usuario' => $usuario,
                    'anio_nacimiento' => $anio_nacimiento,
                    'sexo' => $sexo,
                    'isM' => $sexo === 'M',
                    'isF' => $sexo === 'F',
                    'isO' => $sexo === 'O',
                    'ciudad' => $ciudad,
                    'pais' => $pais,
                    'email' => $email,
                ]);
                return;
            }

            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['foto_perfil']['name']));
            $destination = $uploadDir . '/' . $fileName;

            if (!move_uploaded_file($tmpName, $destination)) {
                $this->render('registerView', [
                    'error' => 'No se pudo guardar la foto de perfil. Intenta de nuevo.',
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'usuario' => $usuario,
                    'anio_nacimiento' => $anio_nacimiento,
                    'sexo' => $sexo,
                    'isM' => $sexo === 'M',
                    'isF' => $sexo === 'F',
                    'isO' => $sexo === 'O',
                    'ciudad' => $ciudad,
                    'pais' => $pais,
                    'email' => $email,
                ]);
                return;
            }

            $foto_perfil = 'uploads/' . $fileName;
        }

        if (!$nombre || !$apellido || !$usuario || !$anio_nacimiento || !$sexo || !$ciudad || !$pais || !$email || !$password) {
            $this->render('registerView', [
                'error' => 'Completa todos los campos para crear tu perfil de jugador.',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        if ($password != $repetirPassword) {
            $this->render('registerView', [
                'error' => 'Las Contraseñas no coincidem',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('registerView', [
                'error' => 'El correo no tiene formato válido. Usa algo como jugador@arcade.com.',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        if ($this->model->usuarioYaExiste($usuario)) {
            $this->render('registerView', [
                'error' => 'El nombre de usuario ya está en uso. Elige otro.',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        if ($this->model->emailYaRegistrado($email)) {
            $this->render('registerView', [
                'error' => 'Ya hay un jugador registrado con ese correo. Inicia sesión o usa otro email.',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        if (!$this->model->registrarUsuario($nombre, $apellido, $usuario, $email, $password, $anio_nacimiento, $sexo, $ciudad, $pais, $foto_perfil)) {
            $this->render('registerView', [
                'error' => 'Error al registrar usuario. Intenta de nuevo.',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'usuario' => $usuario,
                'anio_nacimiento' => $anio_nacimiento,
                'sexo' => $sexo,
                'isM' => $sexo === 'M',
                'isF' => $sexo === 'F',
                'isO' => $sexo === 'O',
                'ciudad' => $ciudad,
                'pais' => $pais,
                'email' => $email,
            ]);
            return;
        }

        $codigo = $this->generarCodigoValidacion();
        $token = $this->generarTokenValidacion();
        $correoEnviado = $this->enviarCodigoValidacion($email, $codigo, $token, $nombre);

        $_SESSION['temp_validation'] = [
            'email' => $email,
            'code' => $codigo,
            'token' => $token,
            'timestamp' => time(),
        ];

        Log::info("GameController::procesarRegistro - nuevo usuario: $email");
        $this->render('validateEmailView', [
            'email' => $email,
            'code' => $codigo,
            'token' => $token,
            'verificationLink' => $this->construirUrlValidacion($email, $token),
            'mailSent' => $correoEnviado,
            'message' => $correoEnviado
                ? 'Se ha enviado un correo con el código de verificación y un enlace de activación.'
                : 'No se pudo enviar el correo desde este entorno. Usa el código que aparece a continuación o el enlace de activación mostrado aquí.',
        ]);
    }

    public function validarCorreo()
    {
        $this->ensureSession();

        $email = trim($this->request->post('email', $this->request->get('email')));
        $code = trim((string) $this->request->post('code', $this->request->get('code')));
        $token = trim((string) $this->request->post('token', $this->request->get('token')));
        $sesionValidacion = $_SESSION['temp_validation'] ?? null;

        if (
            !$email || (!$code && !$token) || !$sesionValidacion
            || ($sesionValidacion['email'] ?? '') !== $email
        ) {
            $this->render('messageView', [
                'title' => 'Validación fallida',
                'message' => 'No encontramos el registro de ese correo o el enlace/código es inválido.',
            ]);
            return;
        }

        if (!empty($sesionValidacion['timestamp']) && $sesionValidacion['timestamp'] + 1800 < time()) {
            unset($_SESSION['temp_validation']);
            $this->render('messageView', [
                'title' => 'Enlace expirado',
                'message' => 'El enlace de validación ha caducado. Vuelve a registrarte para recibir uno nuevo.',
            ]);
            return;
        }

        $codigoValido = !empty($code) && !empty($sesionValidacion['code']) && hash_equals((string) $sesionValidacion['code'], $code);
        $tokenValido = !empty($token) && !empty($sesionValidacion['token']) && hash_equals((string) $sesionValidacion['token'], $token);

        if (!$codigoValido && !$tokenValido) {
            $this->render('messageView', [
                'title' => 'Código o enlace incorrecto',
                'message' => 'El código o el enlace no coinciden. Revisa el correo enviado o vuelve a registrarte si lo necesitas.',
            ]);
            return;
        }

        $this->model->marcarEmailValidado($email);
        unset($_SESSION['temp_validation']);

        $this->render('validateSuccessView', [
            'email' => $email,
        ]);
    }

    public function login()
    {
        $this->ensureSession();

        if ($this->getCurrentUser()) {
            Redirect::to('/tpfinal_mvc/User/perfil');
            return;
        }

        $this->render('loginView');
    }

    public function procesarLogin()
    {
        $this->ensureSession();

        $email = trim($this->request->post('email'));
        $password = trim($this->request->post('password'));

        if (!$email || !$password) {
            $this->render('loginView', [
                'error' => 'Ingresa tu correo y contraseña.',
                'email' => $email,
            ]);
            return;
        }

        // Validar credenciales contra la base de datos
        $usuario = $this->model->validarCredenciales($email, $password);

        if (!$usuario) {
            $this->render('loginView', [
                'error' => 'Correo o contraseña incorrectos. Verifica tus datos.',
                'email' => $email,
            ]);
            return;
        }

        if (!$usuario['email_validado']) {
            $this->render('loginView', [
                'error' => 'Debes validar tu correo antes de entrar al juego.',
                'email' => $email,
            ]);
            return;
        }

        $this->setCurrentUser($email);
        Redirect::to('/tpfinal_mvc/User/perfil');
    }

    public function perfil()
    {
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $this->render('profileView', [
            'user' => $user,
        ]);
    }

    public function logout()
    {
        $this->ensureSession();
        $this->logoutUser();
        unset($_SESSION['puntaje']); // Limpiar puntaje al cerrar sesión


        header('Location: /tpfinal_mvc/User/home');
        exit();
    }
}
