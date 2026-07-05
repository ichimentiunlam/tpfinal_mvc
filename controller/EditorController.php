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

        public function home()
    {

        Redirect::to('/tpfinal_mvc/User/home');
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
        $preguntasSugeridas = $this->model->getPreguntasSugeridas();
        $reportes = $this->model->getPreguntasReportadasNoVistas();
        
        $this->render('correoView', [
        'preguntasSugeridas' => $preguntasSugeridas,
        'reportes' => $reportes
        ]);
    }

    public function aceptarPregunta(){
        $this->ensureSession();
        $id = $this->request->post('id');

        $datos = [
        "pregunta" => $this->request->post('pregunta'),
        "respuestaCorrecta" => $this->request->post('respuestaCorrecta'),
        "respuestaIncorrecta1" => $this->request->post('respuestaIncorrecta1'),
        "respuestaIncorrecta2" => $this->request->post('respuestaIncorrecta2'),
        "respuestaIncorrecta3" => $this->request->post('respuestaIncorrecta3'),
        "id_tipo_pregunta" => $this->request->post('id_tipo')
        ];
        $this->model->createPregunta($datos);
        $this->model->deletePreguntaSugerida($id);
        Redirect::to('/tpfinal_mvc/Editor/correo');
        return;
    }

    public function rechazarPregunta(){
        $this->ensureSession();
        $id = $this->request->post('id');
        $this->model->deletePreguntaSugerida($id);
        Redirect::to('/tpfinal_mvc/Editor/correo');
        return;
    }

    public function marcarReporteComoVisto(){
        $this->ensureSession();
        $id = $this->request->post('id');
        $this->model->marcarReporteComoVisto($id);
        Redirect::to('/tpfinal_mvc/Editor/correo');
        return;
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
