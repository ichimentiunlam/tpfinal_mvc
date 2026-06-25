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

        if (isset($_SESSION['partida'])) {
            unset($_SESSION['partida']);
            return;
        }
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

    public function obtenerDificultadMin($puntaje)
    {
        $dificultadMin = 0;
        if ($puntaje <= 5) {
            $dificultadMin = 0.7;
        }
        return $dificultadMin;
    }

    public function obtenerDificultadMax($puntaje)
    {
        $dificultadMax = 1;
        if ($puntaje > 5) {
            $dificultadMax = 0.7;
        }
        return $dificultadMax;
    }

    public function ruleta()
    {
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }


        if (!isset($_SESSION['partida'])) {

            $data = $this->model->obtenerTipoDePreguntaAleatorio();
            //Se crea una session
            $_SESSION['partida'] = [
                'id_tipo' => $data['id_tipo'],
                'puntaje' => 0,
                'preguntas_respondidas' => [],
                'id_pregunta_actual' => null,
                'inicio' => time(),
                'tiempo_limite' => 60
            ];
        }


        $data = $this->model->obtenerTipoPorId($_SESSION['partida']['id_tipo']);

        $this->render('ruletaView', $data);
    }

    public function jugar()
    {
        $this->ensureSession();

        if (!isset($_SESSION['partida'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }

        // Si no hay pregunta, buscamos una
        if ($_SESSION['partida']['id_pregunta_actual'] === null) {
            $idTipo = $_SESSION['partida']['id_tipo'];
            $preguntasRespondidas = $_SESSION['partida']['preguntas_respondidas'];
            $dificultadMin = $this->obtenerDificultadMin($_SESSION['partida']['puntaje']);
            $dificultadMax = $this->obtenerDificultadMax($_SESSION['partida']['puntaje']);
            $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, $dificultadMax, $dificultadMin);
            if ($pregunta === null) { //Si no encontro pregunta se borran las respondidas
                $_SESSION['partida']['preguntas_respondidas'] = null;
                $preguntasRespondidas = $_SESSION['partida']['preguntas_respondidas'];
                $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, $dificultadMax, $dificultadMin);
            }
            $_SESSION['partida']['id_pregunta_actual'] = $pregunta['id_pregunta'];
        }

        $preguntaData = $this->model->obtenerPreguntaPorId($_SESSION['partida']['id_pregunta_actual']);

        $data = $preguntaData;
        $data['puntaje'] = $_SESSION['partida']['puntaje'];
        $data['tiempo_limite'] = $_SESSION['partida']['tiempo_limite'] ?? 60; // Si es null, pone 60 por defecto



        $this->render('gameView', $data);
    }

    private function cambiarTipoDePregunta()
    {
        if ($_SESSION['partida']['puntaje'] % 5 !== 0) {
            return;
        }

        $cantidadTipos = $this->model->getCantidadDeTiposDePregunta();

        $_SESSION['partida']['id_tipo'] =
            ($_SESSION['partida']['id_tipo'] % $cantidadTipos) + 1;
    }

    public function validarRespuesta()
    {
        $this->ensureSession();
        $id_respuesta = $this->request->post('id_respuesta');
        $id_pregunta = $this->request->post('id_pregunta');
        $esCorrecta = $this->model->esRespuestaCorrecta($id_respuesta);
        $userMail = $this->getCurrentUserEmail();
        if ($esCorrecta) {
            $_SESSION['partida']['puntaje']++;
            $this->cambiarTipoDePregunta();
            $this->model->actualizarVecesRespondidaDeLaPregunta($id_pregunta, $esCorrecta);
            $_SESSION['partida']['preguntas_respondidas'][] = $id_pregunta;
            $_SESSION['partida']['id_pregunta_actual'] = null;
            $_SESSION['partida']['tiempo_limite'] += 25;
            header('Location: /tpfinal_mvc/Game/jugar');
            exit();
        } else {
            $puntajeFinal = $_SESSION['partida']['puntaje'];
            $preguntasRespondidas = $puntajeFinal + 1;
            $this->model->actualizarPuntajeDelUsuario($puntajeFinal, $userMail);
            $this->model->actualizarPreguntasRespondidasDelUsuario($preguntasRespondidas, $userMail);
            $this->model->actualizarVecesRespondidaDeLaPregunta($id_pregunta, $esCorrecta);
            $respuestaCorrecta = $this->model->getRespuestaCorrecta($id_pregunta);
            unset($_SESSION['partida']); //Termina la session
            $this->render('resultadoView', [
                'puntaje' => $puntajeFinal,
                'respuesta_correcta' => $respuestaCorrecta
            ]);
        }
    }

    public function home()
    {

        $this->lobby();
    }
}
