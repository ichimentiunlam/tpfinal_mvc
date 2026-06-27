<?php

class GameModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    // ===================== USUARIOS =====================
    public function registrarUsuario($nombre, $apellido, $usuario, $email, $password, $anio_nacimiento, $sexo, $ciudad, $pais, $foto_perfil)
    {
        $sql = "INSERT INTO usuarios (nombre, apellido, usuario, email, password, anio_nacimiento, sexo, ciudad, pais, foto_perfil, email_validado, coins) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, false, 0)";
        Log::info("SQL: Registrar usuario [$email, $usuario]");
        try {
            return $this->database->execute($sql, [
                $nombre,
                $apellido,
                $usuario,
                $email,
                password_hash($password, PASSWORD_BCRYPT),
                $anio_nacimiento,
                $sexo,
                $ciudad,
                $pais,
                $foto_perfil
            ]);
        } catch (Exception $e) {
            Log::error("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerUsuarioPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        Log::info("SQL: Obtener usuario por email [$email]");
        $filas = $this->database->query($sql, [$email]);
        return !empty($filas) ? $filas[0] : null;
    }

    public function obtenerUsuarioPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        Log::info("SQL: Obtener usuario por id [$id]");
        $filas = $this->database->query($sql, [$id]);
        return !empty($filas) ? $filas[0] : null;
    }

    public function validarCredenciales($email, $password)
    {
        $usuario = $this->obtenerUsuarioPorEmail($email);
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return null;
    }

    public function marcarEmailValidado($email)
    {
        $sql = "UPDATE usuarios SET email_validado = true WHERE email = ?";
        Log::info("SQL: Marcar email validado [$email]");
        return $this->database->execute($sql, [$email]);
    }

    public function emailYaRegistrado($email)
    {
        return $this->obtenerUsuarioPorEmail($email) !== null;
    }

    public function usuarioYaExiste($nombre_usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $filas = $this->database->query($sql, [$nombre_usuario]);
        return !empty($filas);
    }

    public function obtenerTopRanking($sort = 'racha', $order = 'desc')
    {
        $allowedSorts = [
            'racha' => 'puntaje_max',
            'respuestas' => 'preguntas_correctas'
        ];
        $allowedOrders = ['asc', 'desc'];

        $sortColumn = $allowedSorts[$sort] ?? 'puntaje_max';
        $orderDirection = in_array(strtolower($order), $allowedOrders, true) ? strtolower($order) : 'desc';

        $sql = "SELECT id, COALESCE(usuario, nombre) AS usuario, preguntas_correctas, puntaje_max
                FROM usuarios
                ORDER BY {$sortColumn} {$orderDirection}, usuario ASC
                LIMIT 10";

        return $this->database->query($sql);
    }

    // ===================== Juego =====================
    public function obtenerPreguntaPorId($id)
    {
        $sql = "SELECT p.id as id_pregunta, p.pregunta, p.id_tipo_pregunta, 
                
                r1.respuesta as op1, r1.id as id1,
                r2.respuesta as op2, r2.id as id2,
                r3.respuesta as op3, r3.id as id3,
                r4.respuesta as op4, r4.id as id4,
                tp.tipo, tp.color
                FROM preguntas p
                JOIN respuestas r1 ON p.id_respuesta1 = r1.id
                JOIN respuestas r2 ON p.id_respuesta2 = r2.id
                JOIN respuestas r3 ON p.id_respuesta3 = r3.id
                JOIN respuestas r4 ON p.id_respuesta4 = r4.id
                JOIN tipos_pregunta tp ON p.id_tipo_pregunta = tp.id
               	WHERE p.id = ?
                ORDER BY RAND() LIMIT 1";

        $row = $this->database->query($sql, [$id])[0] ?? null;

        if ($row) {
            $opciones = [
                ['id' => $row['id1'], 'texto' => $row['op1']],
                ['id' => $row['id2'], 'texto' => $row['op2']],
                ['id' => $row['id3'], 'texto' => $row['op3']],
                ['id' => $row['id4'], 'texto' => $row['op4']]
            ];
            shuffle($opciones);

            return [
                'id_pregunta' => $row['id_pregunta'],
                'pregunta' => $row['pregunta'],
                'color' => $row['color'],
                'tipo' => $row['tipo'],
                'opciones' => $opciones
            ];
        }
        return null;
    }

    public function obtenerTipoPorId($id)
    {
        $sql = "SELECT id as id_tipo, tipo as tipo_pregunta, color 
                FROM tipos_pregunta 
                WHERE id = ?";

        $row = $this->database->query($sql, [$id])[0] ?? null;

        if ($row) {
            return [
                'id_tipo' => $row['id_tipo'],
                'tipo_pregunta' => $row['tipo_pregunta'],
                'color' => $row['color']
            ];
        }
        return null;
    }

    public function getCantidadDeTiposDePregunta()
    {
        $sql = "SELECT COUNT(*) as cantidadDeTiposDePregunta FROM tipos_pregunta";


        $resultado = $this->database->query($sql)[0] ?? null;

        return $resultado ? (int)$resultado['cantidadDeTiposDePregunta'] : 0;
    }


    public function obtenerTipoDePreguntaAleatorio()
    {
        $sql = "SELECT id as id_tipo,
        tipo as tipo_pregunta,
        color
        FROM tipos_pregunta
        ORDER BY RAND() LIMIT 1";

        $row = $this->database->query($sql)[0] ?? null;

        if ($row) {
            return [
                'id_tipo' => $row['id_tipo'],
                'tipo_pregunta' => $row['tipo_pregunta'],
                'color' => $row['color']
            ];
        }
        return null;
    }

    public function obtenerPreguntaAleatoriaDeUnTipo($idTipo, $preguntasRespondidas, $dificultadMax, $dificultadMin)
    {
        $dificultadSql = "CASE
            WHEN p.veces_respondida = 0 THEN 1
            ELSE p.veces_respondida_correctamente / p.veces_respondida
        END";

        if (empty($preguntasRespondidas)) {
            $sql = "SELECT p.id as id_pregunta, p.pregunta, id_tipo_pregunta,
                $dificultadSql AS dificultad,
                r1.respuesta as op1, r1.id as id1,
                r2.respuesta as op2, r2.id as id2,
                r3.respuesta as op3, r3.id as id3,
                r4.respuesta as op4, r4.id as id4
                FROM preguntas p
                JOIN respuestas r1 ON p.id_respuesta1 = r1.id
                JOIN respuestas r2 ON p.id_respuesta2 = r2.id
                JOIN respuestas r3 ON p.id_respuesta3 = r3.id
                JOIN respuestas r4 ON p.id_respuesta4 = r4.id
               	WHERE id_tipo_pregunta = ? AND $dificultadSql BETWEEN ? AND ?
                ORDER BY RAND() LIMIT 1";
            $params = [$idTipo, $dificultadMin, $dificultadMax];
        } else {

            $respondidas = implode(',', array_fill(0, count($preguntasRespondidas), '?'));
            //implode une los elementos de un array usando un separador
            // y array fill llena todo de ? ? ?
            $sql = "SELECT p.id as id_pregunta, p.pregunta, id_tipo_pregunta,
                $dificultadSql AS dificultad,
                r1.respuesta as op1, r1.id as id1,
                r2.respuesta as op2, r2.id as id2,
                r3.respuesta as op3, r3.id as id3,
                r4.respuesta as op4, r4.id as id4
                FROM preguntas p
                JOIN respuestas r1 ON p.id_respuesta1 = r1.id
                JOIN respuestas r2 ON p.id_respuesta2 = r2.id
                JOIN respuestas r3 ON p.id_respuesta3 = r3.id
                JOIN respuestas r4 ON p.id_respuesta4 = r4.id
               	WHERE id_tipo_pregunta = ?  AND p.id NOT IN ($respondidas) AND $dificultadSql BETWEEN ? AND ?
                ORDER BY RAND() LIMIT 1";

            $params = array_merge([$idTipo], $preguntasRespondidas, [$dificultadMin], [$dificultadMax]);
        }
        $row = $this->database->query($sql, $params)[0] ?? null;

        if ($row) {
            $opciones = [
                ['id' => $row['id1'], 'texto' => $row['op1']],
                ['id' => $row['id2'], 'texto' => $row['op2']],
                ['id' => $row['id3'], 'texto' => $row['op3']],
                ['id' => $row['id4'], 'texto' => $row['op4']]
            ];
            shuffle($opciones);

            return [
                'id_pregunta' => $row['id_pregunta'],
                'pregunta' => $row['pregunta'],
                'opciones' => $opciones
            ];
        }
        return null;
    }

    public function esRespuestaCorrecta($id_respuesta)
    {
        $sql = "SELECT es_correcta FROM respuestas WHERE id = ?";
        $resultado = $this->database->query($sql, [$id_respuesta]);
        return (!empty($resultado) && (int)$resultado[0]['es_correcta'] === 1);
    }

    public function getRespuestaCorrecta($id_pregunta)
    {
        $sql = "SELECT r.respuesta 
                FROM respuestas r
                JOIN preguntas p ON (p.id_respuesta1 = r.id OR p.id_respuesta2 = r.id OR p.id_respuesta3 = r.id OR p.id_respuesta4 = r.id)
                WHERE p.id = ? AND r.es_correcta = 1";
        $resultado = $this->database->query($sql, [$id_pregunta]);
        return !empty($resultado) ? $resultado[0]['respuesta'] : "No disponible";
    }

    public function obtenerOpcionesConCorrecta($id_pregunta)
    {
        $sql = "SELECT r.id, r.respuesta, r.es_correcta
                FROM respuestas r
                JOIN preguntas p ON (p.id_respuesta1 = r.id OR p.id_respuesta2 = r.id OR p.id_respuesta3 = r.id OR p.id_respuesta4 = r.id)
                WHERE p.id = ?";
        return $this->database->query($sql, [$id_pregunta]);
    }

    public function actualizarPuntajeDelUsuario($puntajeFinal, $userMail)
    {
        $sql = "UPDATE usuarios SET puntaje_max = ? 
        WHERE email = ? AND puntaje_max < ?
        ";

        $this->database->execute($sql, [
            $puntajeFinal,
            $userMail,
            $puntajeFinal
        ]);
    }

    public function actualizarPreguntasRespondidasDelUsuario($preguntasRespondidasCorrectamente, $userMail)
    {
        $sql = "UPDATE usuarios SET preguntas_respondidas = preguntas_respondidas + ? + 1,
        preguntas_correctas = preguntas_correctas + ?
        WHERE email = ?
        ";

        $this->database->execute($sql, [
            $preguntasRespondidasCorrectamente,
            $preguntasRespondidasCorrectamente,
            $userMail
        ]);
    }

    public function actualizarVecesRespondidaDeLaPregunta($id_pregunta, $esCorrecta) {
        if($esCorrecta){
            $sql = "UPDATE preguntas 
            SET veces_respondida = veces_respondida + 1,
            veces_respondida_correctamente =  veces_respondida_correctamente + 1
            WHERE id = ?
            ";

              $this->database->execute($sql, [$id_pregunta]);
        }else{
            $sql = "UPDATE preguntas 
            SET veces_respondida = veces_respondida + 1
            WHERE id = ?
            ";

              $this->database->execute($sql, [$id_pregunta]);
        }
        
        
    }

    public function obtenerMonedasUsuarioPorEmail($email)
    {
        $sql = "SELECT coins FROM usuarios WHERE email = ?";
        $resultado = $this->database->query($sql, [$email]);
        if (empty($resultado)) {
            return null;
        }

        return (int)$resultado[0]['coins'];
    }

    public function restarMonedasUsuarioPorEmail($email, $cantidad)
    {
        $sql = "UPDATE usuarios SET coins = coins - ? WHERE email = ? AND coins >= ?";
        return $this->database->execute($sql, [$cantidad, $email, $cantidad]);
    }

    public function sumarMonedasUsuario($email, $cantidad, $amountUsd)
    {
        $this->database->execute(
            "UPDATE usuarios SET coins = coins + ? WHERE email = ?",
            [$cantidad, $email]
        );

        $sql = "INSERT INTO compras_simuladas (user_email, amount_usd, coins_bought, date) VALUES (?, ?, ?, NOW())";
        return $this->database->execute($sql, [$email, $amountUsd, $cantidad]);
    }

    public function createReporteDePregunta($id_pregunta, $mensaje, $email){
        $sql = "INSERT INTO preguntas_reportadas (motivo, id_pregunta, mail_usuario, id_estado) 
        VALUES (? , ? , ? , ?)";

        $this->database->execute($sql, [$mensaje, $id_pregunta, $email, 1]);
    }
}

