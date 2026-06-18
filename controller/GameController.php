<?php

class GameController
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


public function lobby(){
        $this->ensureSession();
        
        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $this->render('lobbyView', [
            'user' => $user,
        ]);
}

public function ranking(){
        $this->ensureSession();
        
        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $this->render('rankingView', [
            'user' => $user,
        ]);
}



public function jugar() {
        $this->ensureSession();
        
        // Inicializamos el puntaje si no existe
        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        $data = $this->model->obtenerPreguntaAleatoria();
        $data['puntaje'] = $_SESSION['puntaje']; 
        
        $this->render('gameView', $data);
    }

    public function validarRespuesta() {
        $this->ensureSession();
        $id_respuesta = $this->request->post('id_respuesta');
        $id_pregunta = $this->request->post('id_pregunta');

        if ($this->model->esRespuestaCorrecta($id_respuesta)) {
            $_SESSION['puntaje'] = ($_SESSION['puntaje'] ?? 0) + 1;
            header('Location: /tpfinal_mvc/Game/jugar');
            exit();
        } else {
            $puntaje_final = $_SESSION['puntaje'] ?? 0;
            $_SESSION['puntaje'] = 0; // Reseteamos al perder
            
            $respuestaCorrecta = $this->model->getRespuestaCorrecta($id_pregunta);
            
            $this->render('resultadoView', [
                'puntaje' => $puntaje_final,
                'respuesta_correcta' => $respuestaCorrecta
            ]);
        }
    }
}