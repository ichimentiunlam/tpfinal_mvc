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
        $sql = "INSERT INTO usuarios (nombre, apellido, usuario, email, password, anio_nacimiento, sexo, ciudad, pais, foto_perfil, email_validado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, false)";
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
}
