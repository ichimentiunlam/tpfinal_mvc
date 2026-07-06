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

    private function jsonResponse(array $data, int $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    private function cobrarMonedas($email, int $cost)
    {
        if (!$email) {
            return ['success' => false, 'error' => 'Sesión inválida'];
        }

        $coins = $this->model->obtenerMonedasUsuarioPorEmail($email);
        if ($coins === null) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        if ($coins < $cost) {
            return ['success' => false, 'error' => 'No tenés suficientes monedas'];
        }

        $updated = $this->model->restarMonedasUsuarioPorEmail($email, $cost);
        if (!$updated) {
            return ['success' => false, 'error' => 'No se pudo descontar el saldo'];
        }

        $newBalance = $this->model->obtenerMonedasUsuarioPorEmail($email);
        return ['success' => true, 'coins' => $newBalance];
    }


    public function lobby()
    {
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $mensaje = $_SESSION['partida']['mensaje'] ?? null;
        $_SESSION['partida']['mensaje'] = null;
        $categorias = $this->model->getTipoPregunta();

        $this->render('lobbyView', [
        'user' => $user,
        'mensaje' => $mensaje,
        'categorias' => $categorias
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

        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'racha';
        $order = isset($_GET['order']) ? $_GET['order'] : 'desc';

        $ranking = $this->model->obtenerTopRanking($sort, $order);
        $ranking = $this->formatRankingData($ranking);

        $orderRacha = ($sort === 'racha' && $order === 'desc') ? 'asc' : 'desc';
        $orderRespuestas = ($sort === 'respuestas' && $order === 'desc') ? 'asc' : 'desc';

        $this->render('rankingView', [
            'user' => $user,
            'usuariosRanking' => $ranking,
            'rankingSort' => $sort,
            'rankingOrder' => $order,
            'orderRacha' => $orderRacha,
            'orderRespuestas' => $orderRespuestas,
        ]);
    }

    private function formatRankingData(array $ranking)
    {
        foreach ($ranking as $index => &$row) {
            $rank = $index + 1;
            $row['rank'] = $rank;
            $row['rankClass'] = $rank === 1 ? 'podium-1' : ($rank === 2 ? 'podium-2' : ($rank === 3 ? 'podium-3' : ''));
            $row['badgeClass'] = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
            $row['respuestas_totales'] = $row['preguntas_correctas'] ?? 0;
            $row['racha_max'] = $row['puntaje_max'] ?? 0;
        }
        return $ranking;
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


        if (!isset($_SESSION['partida']) || empty($_SESSION['partida']['id_tipo'])) {
            $data = $this->model->obtenerTipoDePreguntaAleatorio();
            // Se crea una session
            $_SESSION['partida'] = [
                'id_tipo' => $data['id_tipo'] ?? 1,
                'puntaje' => 0,
                'preguntas_respondidas' => [],
                'id_pregunta_actual' => null,
                'inicio' => time(),
                'tiempo_limite' => 60,
                'comodin_magic_used' => false,
                'comodin_change_used' => false,
                'comodin_5050_usado' => false,
                'comodin_5050_activo' => false,
                'comodin_5050_opciones' => null,
                'comodin_5050_pregunta' => null,
                'mensaje' => null
            ];
        }

        if (empty($_SESSION['partida']['id_tipo'])) {
            $_SESSION['partida']['id_tipo'] = 1;
        }

        $data = $this->model->obtenerTipoPorId($_SESSION['partida']['id_tipo']);

        $this->render('ruletaView', $data);
    }

public function jugar()
    {
        $this->ensureSession();

        if (!isset($_SESSION['partida']) || empty($_SESSION['partida']['id_tipo'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }

        // Si no hay pregunta, buscamos una
        if (empty($_SESSION['partida']['id_pregunta_actual'])) {
            $_SESSION['partida']['comodin_5050_activo'] = false;
            $_SESSION['partida']['comodin_5050_opciones'] = null;
            $_SESSION['partida']['comodin_5050_pregunta'] = null;

            $idTipo = $_SESSION['partida']['id_tipo'];
            $preguntasRespondidas = $_SESSION['partida']['preguntas_respondidas'] ?? [];
            $dificultadMin = $this->obtenerDificultadMin($_SESSION['partida']['puntaje'] ?? 0);
            $dificultadMax = $this->obtenerDificultadMax($_SESSION['partida']['puntaje'] ?? 0);
            $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, $dificultadMax, $dificultadMin);
            
            if ($pregunta === null) { //Si no encontro pregunta se borran las respondidas
                $_SESSION['partida']['preguntas_respondidas'] = null;
                $preguntasRespondidas = $_SESSION['partida']['preguntas_respondidas'];
                $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, $dificultadMax, $dificultadMin);
                if($pregunta === null){ //Si aun asi no encontro ninguna pregunta se selecciona cualquier pregunta de la base de datos
                    $pregunta = $this->model->obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, 1, 0);
                }
            }
            $_SESSION['partida']['id_pregunta_actual'] = $pregunta['id_pregunta'];
            
            //  NUEVO: GUARDAMOS LA HORA EXACTA EN LA QUE EMPEZÓ LA PREGUNTA 
            $_SESSION['partida']['timestamp_inicio_pregunta'] = time();
        }

        if (empty($_SESSION['partida']['id_pregunta_actual'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }

        $preguntaData = $this->model->obtenerPreguntaPorId($_SESSION['partida']['id_pregunta_actual']);

        if (empty($preguntaData)) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }

        // --- NUEVO: CALCULAMOS CUÁNTO TIEMPO PASÓ REALMENTE ---
        $tiempo_base = $_SESSION['partida']['tiempo_limite'] ?? 60;
        //  fallback a time() por si es la primera vez que entra a la variable
        $timestamp_inicio = $_SESSION['partida']['timestamp_inicio_pregunta'] ?? time(); 
        $segundos_pasados = time() - $timestamp_inicio;
        $tiempo_restante = $tiempo_base - $segundos_pasados;

        // Si recargó la página (F5) pero ya se le había acabado el tiempo real:
        if ($tiempo_restante <= 0) {
            Redirect::to('/tpfinal_mvc/Game/timeout');
            return;
        }

        
        $data = $preguntaData;
        $data['puntaje'] = $_SESSION['partida']['puntaje'] ?? 0;
        $data['coins'] = $this->getCurrentUserEmail() ? $this->model->obtenerMonedasUsuarioPorEmail($this->getCurrentUserEmail()) : 0;
        $data['comodin_magic_used'] = $_SESSION['partida']['comodin_magic_used'] ?? false;
        $data['comodin_change_used'] = $_SESSION['partida']['comodin_change_used'] ?? false;
        $data['comodin_5050_used'] = $_SESSION['partida']['comodin_5050_usado'] ?? false;
        $data['comodin_5050_activo'] = $_SESSION['partida']['comodin_5050_activo'] ?? false;
        $data['mensaje'] = $_SESSION['partida']['mensaje'] ?? null;
        $_SESSION['partida']['mensaje'] = null;
        
        if (!empty($_SESSION['partida']['comodin_5050_activo']) && $_SESSION['partida']['comodin_5050_pregunta'] === $_SESSION['partida']['id_pregunta_actual']) {
            $data['opciones'] = $_SESSION['partida']['comodin_5050_opciones'];
        }
        
        
        $data['tiempo_limite'] = $tiempo_restante; 
        
        $data['show_time_bonus'] = !empty($_SESSION['partida']['show_time_bonus']);
        unset($_SESSION['partida']['show_time_bonus']);

        $this->render('gameView', $data);
    }

    private function cambiarTipoDePregunta()
    {
        if (!isset($_SESSION['partida']['puntaje']) || $_SESSION['partida']['puntaje'] % 5 !== 0) {
            return;
        }

        if (empty($_SESSION['partida']['id_tipo'])) {
            return;
        }

        $cantidadTipos = $this->model->getCantidadDeTiposDePregunta();

        $_SESSION['partida']['id_tipo'] =
            ($_SESSION['partida']['id_tipo'] % $cantidadTipos) + 1;
    }

    public function useWildcardChange()
    {
        $this->ensureSession();

        $email = $this->getCurrentUserEmail();
        if (!$email || !isset($_SESSION['partida'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Sesión inválida'], 400);
        }

        if (!empty($_SESSION['partida']['comodin_change_used'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Ya usaste Change Question en esta partida'], 400);
        }

        $payment = $this->cobrarMonedas($email, 2);
        if (!$payment['success']) {
            $this->jsonResponse(['success' => false, 'error' => $payment['error']], 402);
        }
        
        // Guardar el tiempo actual antes de hacer la recarga
        $tiempo_restante = (int) $this->request->post('tiempo_restante');
        if ($tiempo_restante > 0) {
            $_SESSION['partida']['tiempo_limite'] = $tiempo_restante;
        }

        $_SESSION['partida']['id_pregunta_actual'] = null;
        $_SESSION['partida']['comodin_change_used'] = true;
        $_SESSION['partida']['comodin_5050_activo'] = false;
        $_SESSION['partida']['comodin_5050_opciones'] = null;
        $_SESSION['partida']['comodin_5050_pregunta'] = null;

        $this->jsonResponse([
            'success' => true,
            'type' => 'change',
            'coins' => $payment['coins']
        ]);
    }

    
    public function useWildcard5050()
    {
        $this->ensureSession();

        $email = $this->getCurrentUserEmail();
        if (!$email || !isset($_SESSION['partida']) || empty($_SESSION['partida']['id_pregunta_actual'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Sesión inválida o pregunta no encontrada'], 400);
        }

        if (!empty($_SESSION['partida']['comodin_5050_used'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Ya usaste 50/50 en esta partida'], 400);
        }

        $payment = $this->cobrarMonedas($email, 2);
        if (!$payment['success']) {
            $this->jsonResponse(['success' => false, 'error' => $payment['error']], 402);
        }

        $idPregunta = $_SESSION['partida']['id_pregunta_actual'];
        $opciones = $this->model->obtenerOpcionesConCorrecta($idPregunta);
        $correcta = array_values(array_filter($opciones, fn($opcion) => (int)$opcion['es_correcta'] === 1))[0] ?? null;
        $incorrectas = array_values(array_filter($opciones, fn($opcion) => (int)$opcion['es_correcta'] !== 1));

        if (!$correcta || count($incorrectas) < 1) {
            $this->jsonResponse(['success' => false, 'error' => 'No se pudo aplicar 50/50'], 500);
        }

        $incorrecta = $incorrectas[array_rand($incorrectas)];
        $disabledIncorrectIds = array_column(array_filter($incorrectas, fn($opcion) => $opcion['id'] !== $incorrecta['id']), 'id');

        $_SESSION['partida']['comodin_5050_usado'] = true;
        $_SESSION['partida']['comodin_5050_activo'] = false;
        $_SESSION['partida']['comodin_5050_opciones'] = null;
        $_SESSION['partida']['comodin_5050_pregunta'] = null;

        $this->jsonResponse([
            'success' => true,
            'type' => '5050',
            'coins' => $payment['coins'],
            'correctId' => $correcta['id'],
            'disabledIds' => $disabledIncorrectIds
        ]);
    }

    public function useWildcardResolution()
    {
        $this->ensureSession();

        $email = $this->getCurrentUserEmail();
        if (!$email || !isset($_SESSION['partida']) || empty($_SESSION['partida']['id_pregunta_actual'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Sesión inválida o pregunta no encontrada'], 400);
        }

        if (!empty($_SESSION['partida']['comodin_magic_used'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Ya usaste Resolución Mágica en esta partida'], 400);
        }

        $payment = $this->cobrarMonedas($email, 10);
        if (!$payment['success']) {
            $this->jsonResponse(['success' => false, 'error' => $payment['error']], 402);
        }

        $idPregunta = $_SESSION['partida']['id_pregunta_actual'];
        $opciones = $this->model->obtenerOpcionesConCorrecta($idPregunta);
        $correcta = array_values(array_filter($opciones, fn($opcion) => (int)$opcion['es_correcta'] === 1))[0] ?? null;

        if (!$correcta) {
            $this->jsonResponse(['success' => false, 'error' => 'No se pudo identificar la respuesta correcta'], 500);
        }

        $_SESSION['partida']['comodin_magic_used'] = true;

        $this->jsonResponse([
            'success' => true,
            'type' => 'resolution',
            'coins' => $payment['coins'],
            'correctId' => $correcta['id']
        ]);
    }

    public function usarCincuentaCincuenta()
    {
        return $this->useWildcard5050();
    }

   public function validarRespuesta()
    {
        $this->ensureSession();

        if (!isset($_SESSION['partida'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }

        $id_respuesta = $this->request->post('id_respuesta');
        $id_pregunta = $this->request->post('id_pregunta');
        
        
        $tiempo_restante = (int) $this->request->post('tiempo_restante'); 
        
        $esCorrecta = $this->model->esRespuestaCorrecta($id_respuesta);
        $userMail = $this->getCurrentUserEmail();
        
        if ($esCorrecta) {
            $_SESSION['partida']['puntaje']++;
            $this->cambiarTipoDePregunta();
            $this->model->actualizarVecesRespondidaDeLaPregunta($id_pregunta, $esCorrecta);
            $_SESSION['partida']['preguntas_respondidas'][] = $id_pregunta;
            $_SESSION['partida']['id_pregunta_actual'] = null;
            
            // Le sumamos los 25s al tiempo sobrante real (y evitamos que baje de 0 por las dudas)
            $tiempo_guardado = $tiempo_restante > 0 ? $tiempo_restante : 0;
            $_SESSION['partida']['tiempo_limite'] = $tiempo_guardado + 25;
            
            $_SESSION['partida']['show_time_bonus'] = true;
            header('Location: /tpfinal_mvc/Game/jugar');
            exit();
        } else {
            $puntajeFinal = $_SESSION['partida']['puntaje'];
            $preguntasRespondidas = $puntajeFinal + 1;
            $this->model->actualizarPuntajeDelUsuario($puntajeFinal, $userMail);
            $this->model->actualizarPreguntasRespondidasDelUsuario($preguntasRespondidas, $userMail);
            $this->model->actualizarVecesRespondidaDeLaPregunta($id_pregunta, $esCorrecta);
            $respuestaCorrecta = $this->model->getRespuestaCorrecta($id_pregunta);
            unset($_SESSION['partida']); 
            $this->render('resultadoView', [
                'puntaje' => $puntajeFinal,
                'respuesta_correcta' => $respuestaCorrecta
            ]);
        }
    }

    public function timeout()
    {
        $this->ensureSession();
        $userMail = $this->getCurrentUserEmail();
        $puntajeFinal = $_SESSION['partida']['puntaje'] ?? 0;
        $idPregunta = $_SESSION['partida']['id_pregunta_actual'] ?? null;
        $respuestaCorrecta = $idPregunta ? $this->model->getRespuestaCorrecta($idPregunta) : '';

        if ($userMail) {
            $preguntasRespondidas = $puntajeFinal + 1;
            $this->model->actualizarPuntajeDelUsuario($puntajeFinal, $userMail);
            $this->model->actualizarPreguntasRespondidasDelUsuario($preguntasRespondidas, $userMail);
            if ($idPregunta) {
                $this->model->actualizarVecesRespondidaDeLaPregunta($idPregunta, false);
            }
        }

        unset($_SESSION['partida']);

        $this->render('resultadoView', [
            'puntaje' => $puntajeFinal,
            'respuesta_correcta' => $respuestaCorrecta
        ]);
    }

    public function home()
    {

        $this->lobby();
    }

    public function reportarPregunta(){
        $this->ensureSession();

        if (!isset($_SESSION['partida'])) {
            Redirect::to('/tpfinal_mvc/Game/ruleta');
            return;
        }
        $email = $this->getCurrentUserEmail();
        $mensaje = $this->request->post('mensaje');
        $id_pregunta = $this->request->post('id_pregunta');

        $this->model->createReporteDePregunta($id_pregunta, $mensaje, $email);

        $_SESSION['partida']['mensaje'] = "¡Gracias! Tu reporte fue enviado correctamente.";

        Redirect::to('/tpfinal_mvc/Game/jugar');
    }

    public function sugerirPregunta(){
        $this->ensureSession();

        
        $pregunta = $this->request->post('nuevaPregunta');
        $respuestaCorrecta = $this->request->post('respuestaCorrecta');
        $respuestaIncorrecta1 = $this->request->post('incorrecta1');
        $respuestaIncorrecta2 = $this->request->post('incorrecta2');
        $respuestaIncorrecta3 = $this->request->post('incorrecta3');
        $id_tipo_pregunta = $this->request->post('id_tipo_pregunta');

        if($pregunta == null || $respuestaCorrecta == null || 
        $respuestaIncorrecta1 == null || 
        $respuestaIncorrecta2 == null || 
        $respuestaIncorrecta3 == null || 
        $id_tipo_pregunta == null){
            $_SESSION['partida']['mensaje'] = "Se deben llenar todos los campos";
            Redirect::to('/tpfinal_mvc/Game/lobby');
            return;
        }

        $this->model->createPreguntaSugerida($pregunta, $respuestaCorrecta, $respuestaIncorrecta1, $respuestaIncorrecta2, $respuestaIncorrecta3, $id_tipo_pregunta);

        $_SESSION['partida']['mensaje'] = "¡Gracias! Tu pregunta fue sugerida correctamente.";

        Redirect::to('/tpfinal_mvc/Game/lobby');
    }
    
}
