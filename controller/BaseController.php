<?php

class BaseController
{
    protected $model;
    protected $renderer;
    protected $request;

    public function __construct($model, $renderer, $request)
    {
        $this->model    = $model;
        $this->renderer = $renderer;
        $this->request  = $request;
    }

    protected function ensureSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }
    }

    protected function getCurrentUserEmail()
    {
        return $_SESSION['current_user'] ?? null;
    }

    protected function getCurrentUser()
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

    protected function setCurrentUser($email)
    {
        $_SESSION['current_user'] = $email;
    }

    protected function getNavData()
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

    protected function render($viewName, $data = [])
    {
        $this->ensureSession();
        $this->renderer->render($viewName, array_merge($this->getNavData(), $data));
    }

    protected function jsonResponse(array $data, int $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}
