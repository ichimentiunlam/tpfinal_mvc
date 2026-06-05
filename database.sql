create schema quiz_game;
use quiz_game;

-- Tabla de usuarios registrados
create table quiz_game.usuarios
(
    id            int auto_increment primary key,
    nombre        varchar(100)                                 not null,
    apellido      varchar(100)                                 not null,
    anio_nacimiento int                                          not null,
    sexo         enum('M', 'F', 'O')                             not null,
    ciudad        varchar(100)                                 not null,
    pais         varchar(100)                                 not null,
    email         varchar(100) unique                       not null,
    email_validado boolean      default false               not null,
    password      varchar(255)                              not null,
    usuario varchar(50) unique                       not null,
    foto_perfil varchar(255)                              null,
    fecha_registro timestamp    default current_timestamp()  not null
);