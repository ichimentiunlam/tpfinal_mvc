<?php

class EditorController extends BaseController
{
    public function home()
    {

        Redirect::to('/tpfinal_mvc/User/home');
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
        $categorias = $this->model->getTipoPregunta();
        $categoriasSugeridas = $this->model->getTipoPreguntaSugeridas();
        $preguntas = $this->model->getTodasLasPreguntasAceptadas();

        foreach ($preguntas as $i => &$pregunta) {
        $pregunta['numero'] = $i + 1;
        }

        $this->render('modificarView',[ 'preguntas' => $preguntas,
        'categorias' => $categorias,
        'categoriasSugeridas' => $categoriasSugeridas
        ]);
    }

    public function detallePregunta(){
        $this->ensureSession();
        $this->necesitaEditor();
        $id = $_GET['id'];
        $pregunta = $this->model->obtenerPreguntaPorId($id);
        $categorias = $this->model->getTipoPregunta();
        $contadorDeRespuestas = 1;

        foreach ($pregunta['opciones'] as $opcion) {
        if ($this->model->esRespuestaCorrecta($opcion['id'])) {
            $pregunta['respuestaCorrecta'] = $opcion['texto'];
            $pregunta['id_respuestaCorrecta'] = $opcion['id'];
        }

        if (!$this->model->esRespuestaCorrecta($opcion['id'])) {
            $pregunta['incorrecta' . $contadorDeRespuestas] = $opcion['texto'];
            $pregunta['id_incorrecta' . $contadorDeRespuestas] = $opcion['id'];
            $contadorDeRespuestas++;
        }
        
        }


        $this->render('detallePreguntaView',[ 'pregunta' => $pregunta,
         'id' => $id,
         'categorias' => $categorias]);
    }

    public function eliminarPregunta(){
        $this->ensureSession();
        $id = $this->request->post('id');
        $this->model->deletePregunta($id);
        Redirect::to('/tpfinal_mvc/Editor/modificar');
        return;
    }

    public function modificarPregunta(){
        $this->ensureSession();

        $datos = [
        "id" => $this->request->post('id'),
        "nuevaPregunta" => $this->request->post('nuevaPregunta'),
        "respuestaCorrecta" => $this->request->post('respuestaCorrecta'),
        "incorrecta1" => $this->request->post('incorrecta1'),
        "incorrecta2" => $this->request->post('incorrecta2'),
        "incorrecta3" => $this->request->post('incorrecta3'),
        "id_respuestaCorrecta" => $this->request->post('id_respuestaCorrecta'),
        "id_incorrecta1" => $this->request->post('id_incorrecta1'),
        "id_incorrecta2" => $this->request->post('id_incorrecta2'),
        "id_incorrecta3" => $this->request->post('id_incorrecta3'),
        "id_tipo_pregunta" => $this->request->post('id_tipo_pregunta')
        ];


        $this->model->updatePregunta($datos);
        Redirect::to('/tpfinal_mvc/Editor/modificar');
        return;
    }

    public function crearPregunta(){
        $this->ensureSession();

        $datos = [
        "pregunta" => $this->request->post('nuevaPregunta'),
        "respuestaCorrecta" => $this->request->post('respuestaCorrecta'),
        "respuestaIncorrecta1" => $this->request->post('incorrecta1'),
        "respuestaIncorrecta2" => $this->request->post('incorrecta2'),
        "respuestaIncorrecta3" => $this->request->post('incorrecta3'),
        "id_tipo_pregunta"=> $this->request->post('id_tipo_pregunta')
        ];

        $nombreCategoria = $this->request->post('nombreCategoria');
        $colorCategoria = $this->request->post('colorCategoria');
        
        if($datos['pregunta'] == null || $datos['respuestaCorrecta'] == null || 
        $datos['respuestaIncorrecta1'] == null || 
        $datos['respuestaIncorrecta2'] == null || 
        $datos['respuestaIncorrecta3'] == null || 
        $datos['id_tipo_pregunta'] == null){
            $_SESSION['partida']['mensaje'] = "Se deben llenar todos los campos";
            Redirect::to('/tpfinal_mvc/Game/lobby');
            return;
        }

        if($datos['id_tipo_pregunta'] == null && $nombreCategoria == null){
            $_SESSION['partida']['mensaje'] = "Se deben elegir categoria";
            Redirect::to('/tpfinal_mvc/Game/lobby');
            return;
        }
        //El value de la option "nueva categoria" es nueva
        if($datos['id_tipo_pregunta'] == 'nueva'){
            $datos['id_tipo_pregunta'] = $this->model->createCategoria($nombreCategoria, $colorCategoria);
        }

        $this->model->createPregunta($datos);

        $_SESSION['partida']['mensaje'] = "¡Gracias! Tu pregunta fue sugerida correctamente.";

        Redirect::to('/tpfinal_mvc/Editor/modificar');
    
    }

    public function eliminarCategoria(){
        $this->ensureSession();
        $id = $this->request->post('id');
        $this->model->deleteCategoria($id);
        Redirect::to('/tpfinal_mvc/Editor/modificar');
        return;
    }

    public function modificarCategoria(){
        $this->ensureSession();

        $datos = [
        "id" => $this->request->post('id'),
        "nombreCategoria" => $this->request->post('nombreCategoria'),
        "colorCategoria" => $this->request->post('colorCategoria')
        ];

        $this->model->updateCategoria($datos);
        Redirect::to('/tpfinal_mvc/Editor/modificar');
        return;
    }
}
