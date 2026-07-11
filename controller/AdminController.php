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
        $data = "hola";

        $this->render('estadisticasView', [$data]
        );
    }

}
