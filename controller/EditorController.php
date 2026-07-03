<?php

class EditorController
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


        $usuario = $this->model->obtenerUsuarioPorEmail($email);
        if ($usuario && $usuario['email_validado']) {
            $usuario['validated'] = $usuario['email_validado'];
            $usuario['username'] = $usuario['usuario'] ?? $usuario['nombre'];
            return $usuario;
        }
        return null;
    }

   private function getNavData()
    {
        $user = $this->getCurrentUser();
        $rol = $user['rol'] ?? ''; 
        return [
            'loggedIn' => (bool) $user,
            'username' => $user['usuario'] ?? $user['nombre'] ?? null,
            'validated' => $user['email_validado'] ?? false,
            'esAdmin' => $rol === 'Administrador',
            'esEditor' => in_array($rol, ['Administrador', 'Editor'])
        ];
    }

    private function render($viewName, $data = [])
    {
        $this->ensureSession();
        $this->renderer->render($viewName, array_merge($this->getNavData(), $data));
    }

    private function necesitaEditor(){
        $user = $this->getCurrentUser();
        $rol = $user['rol'] ?? ''; 
        if($rol === 'Comun'){
            Redirect::to('/tpfinal_mvc/User/home');
            exit;
        }
    }

    public function correo(){
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }
        $this->necesitaEditor();
        $data = "hola";

        $this->render('correoView', [$data]
        );
    }

    public function modificar(){
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $this->necesitaEditor();
        

        $data = "hola";

        $this->render('modificarView', [$data]
        );
    }
}
