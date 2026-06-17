<?php

class UserController
{
    private $model;
    private $renderer;
    private $request;

    public function __construct($model, $renderer, $request)
    {
        $this->model    = $model;
        $this->renderer = $renderer;
        $this->request  = $request;
    }

        private function ensureSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }
    }

    private function getCurrentUserEmail()
    {
        return $_SESSION['current_user'] ?? null;
    }

    private function getCurrentUser()
    {
        $email = $this->getCurrentUserEmail();
        if (!$email) {
            return null;
        }

        // Obtener usuario de la base de datos
        $usuario = $this->model->obtenerUsuarioPorEmail($email);
        if ($usuario && $usuario['email_validado']) {
            $usuario['validated'] = $usuario['email_validado'];
            $usuario['username'] = $usuario['usuario'] ?? $usuario['nombre'];
            return $usuario;
        }
        return null;
    }

    private function setCurrentUser($email)
    {
        $_SESSION['current_user'] = $email;
    }

    private function logoutUser()
    {
        unset($_SESSION['current_user']);
    }

    private function getNavData()
    {
        $user = $this->getCurrentUser();
        return [
            'loggedIn' => (bool) $user,
            'username' => $user['usuario'] ?? $user['nombre'] ?? null,
            'validated' => $user['email_validado'] ?? false,
        ];
    }

    private function render($viewName, $data = [])
    {
        $this->ensureSession();
        $this->renderer->render($viewName, array_merge($this->getNavData(), $data));
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

                if ( $password != $repetirPassword) {
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

        // Guardar datos temporales en sesión para validación
        $codigo = substr(str_shuffle('0123456789'), 0, 6);
        $_SESSION['temp_validation'] = [
            'email' => $email,
            'code' => $codigo,
            'timestamp' => time(),
        ];

        Log::info("GameController::procesarRegistro - nuevo usuario: $email");
        $this->render('validateEmailView', [
            'email' => $email,
            'code' => $codigo,
        ]);
    }

    public function validarCorreo()
    {
        $this->ensureSession();

        $email = $this->request->get('email');
        $code = $this->request->get('code');

        if (!$email || !$code || !isset($_SESSION['temp_validation']) 
            || $_SESSION['temp_validation']['email'] !== $email) {
            $this->render('messageView', [
                'title' => 'Validación fallida',
                'message' => 'No encontramos el registro de ese correo o el código es inválido.',
            ]);
            return;
        }

        if ($_SESSION['temp_validation']['code'] !== $code) {
            $this->render('messageView', [
                'title' => 'Código incorrecto',
                'message' => 'El código no coincide. Revisa el enlace enviado o regístrate de nuevo.',
            ]);
            return;
        }

        // Marcar email como validado en la base de datos
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
    
    
    header('Location: /tpfinal_mvc/User/home'); // Redirigir a la página de inicio después de cerrar sesión
    exit();
}
}
?>