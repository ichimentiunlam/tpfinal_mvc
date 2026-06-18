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


    public function lobby()
    {
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

    public function ranking()
    {
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

    public function ruleta()
    {
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        // SI NO EXISTE PARTIDA → CREARLA
        if (!isset($_SESSION['partida'])) {

            $data = $this->model->obtenerTipoDePreguntaAleatorio();

            $_SESSION['partida'] = [
                'id_tipo' => $data['id_tipo'],
                'puntaje' => 0,
                'preguntas_respondidas' => [],
                'id_pregunta_actual' => null,
                'inicio' => time()
            ];
        }

        // SI EXISTE → USARLA
        $data = $this->model->obtenerTipoPorId($_SESSION['partida']['id_tipo']);

        $this->render('ruletaView', $data);
    }

    public function jugar()
    {
        $this->ensureSession();
        //Comprueba que exista la sesion
        if (!isset($_SESSION['partida'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }
        //Comprueba que id_pregunta_actual este vacio, si esta guarda una pregunta nueva, si no da la que ya esta
        if ($_SESSION['partida']['id_pregunta_actual'] === null) {

            $idTipo = $_SESSION['partida']['id_tipo'];
            $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo);
            $_SESSION['partida']['id_pregunta_actual'] = $pregunta['id_pregunta'];
        }

        $data = $this->model->obtenerPreguntaPorId($_SESSION['partida']['id_pregunta_actual']);

        $data['puntaje'] = $_SESSION['partida']['puntaje'];

        $this->render('gameView', $data);
    }

    public function validarRespuesta()
    {
        $this->ensureSession();
        $id_respuesta = $this->request->post('id_respuesta');
        $id_pregunta = $this->request->post('id_pregunta');

        if ($this->model->esRespuestaCorrecta($id_respuesta)) {
            $_SESSION['partida']['puntaje']++;
            $_SESSION['partida']['id_pregunta_actual'] = null;
            header('Location: /tpfinal_mvc/Game/jugar');
            exit();
        } else {
            $puntajeFinal = $_SESSION['partida']['puntaje'];
            $_SESSION['puntaje'] = 0; // Reseteamos al perder

            $respuestaCorrecta = $this->model->getRespuestaCorrecta($id_pregunta);
            unset($_SESSION['partida']);
            $this->render('resultadoView', [
                'puntaje' => $puntajeFinal,
                'respuesta_correcta' => $respuestaCorrecta
            ]);
        }
    }
}
