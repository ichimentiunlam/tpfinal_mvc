<?php

class AdminController extends BaseController
{

    private function necesitaAdmin(){
        $user = $this->getCurrentUser();
        $rol = $user['rol'] ?? ''; 
        if($rol != 'Administrador'){
            Redirect::to('/tpfinal_mvc/User/home');
            exit;
        }
    }

    public function estadisticas(){
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }
        $this->necesitaAdmin();

        $periodosValidos = ['dia', 'semana', 'mes', 'anio', 'todo'];
        $periodo = $_GET['periodo'] ?? 'todo';
        if (!in_array($periodo, $periodosValidos, true)) {
            $periodo = 'todo';
        }

        $stats = $this->model->getEstadisticas($periodo);
        $stats['user'] = $user;

        $this->render('estadisticasView', $stats);
    }

}
