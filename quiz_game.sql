-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3307
-- Tiempo de generación: 29-06-2026 a las 16:17:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `quiz_game`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_simuladas`
--

CREATE TABLE `compras_simuladas` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `amount_usd` decimal(6,2) NOT NULL DEFAULT 0.00,
  `coins_bought` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras_simuladas`
--

INSERT INTO `compras_simuladas` (`id`, `user_email`, `amount_usd`, `coins_bought`, `date`) VALUES
(1, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:21:22'),
(2, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:21:24'),
(3, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:21:26'),
(4, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:26:28'),
(5, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:26:29'),
(6, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:26:30'),
(7, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:26:32'),
(8, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:31:11'),
(9, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:31:11'),
(10, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:34:41'),
(11, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:34:43'),
(12, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:34:43'),
(13, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:38:28'),
(14, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:38:29'),
(15, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:38:29'),
(16, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:42:23'),
(17, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:42:24'),
(18, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:46:38'),
(19, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:46:39'),
(20, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:46:40'),
(21, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:48:39'),
(22, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:48:42'),
(23, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:20'),
(24, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:21'),
(25, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:22'),
(26, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:23'),
(27, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:23'),
(28, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:24'),
(29, 'Testy@mail.com', 1.00, 10, '2026-06-26 05:52:24'),
(30, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:32'),
(31, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:34'),
(32, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:35'),
(33, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:35'),
(34, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:36'),
(35, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:23:36'),
(36, 'Testy@mail.com', 1.00, 10, '2026-06-26 06:25:56'),
(37, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:37'),
(38, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:38'),
(39, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:39'),
(40, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:39'),
(41, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:39'),
(42, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:39'),
(43, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:40'),
(44, 'testy@mail.com', 1.00, 10, '2026-06-26 13:52:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id` int(11) NOT NULL,
  `pregunta` varchar(120) NOT NULL,
  `id_respuesta1` int(11) NOT NULL,
  `id_respuesta2` int(11) NOT NULL,
  `id_respuesta3` int(11) NOT NULL,
  `id_respuesta4` int(11) NOT NULL,
  `veces_respondida` int(11) NOT NULL DEFAULT 0,
  `veces_respondida_correctamente` int(11) NOT NULL DEFAULT 0,
  `id_tipo_pregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id`, `pregunta`, `id_respuesta1`, `id_respuesta2`, `id_respuesta3`, `id_respuesta4`, `veces_respondida`, `veces_respondida_correctamente`, `id_tipo_pregunta`) VALUES
(1, '¿En qué año comenzó la Segunda Guerra Mundial?', 1, 2, 3, 4, 2, 0, 1),
(2, '¿Quién fue el primer presidente de Argentina?', 5, 6, 7, 8, 6, 4, 1),
(3, '¿Qué civilización construyó las pirámides de Giza?', 9, 10, 11, 12, 1, 0, 1),
(4, '¿En qué año cayó el Imperio Romano de Occidente?', 13, 14, 15, 16, 1, 0, 1),
(5, '¿Quién descubrió América en 1492?', 17, 18, 19, 20, 2, 1, 1),
(6, '¿Cuál es el río más largo del mundo?', 21, 22, 23, 24, 1, 0, 2),
(7, '¿Cuál es la capital de Australia?', 25, 26, 27, 28, 1, 0, 2),
(8, '¿En qué continente está Egipto?', 29, 30, 31, 32, 2, 0, 2),
(9, '¿Cuál es el país más grande del mundo?', 33, 34, 35, 36, 8, 5, 2),
(10, '¿Cuál es el océano más grande?', 37, 38, 39, 40, 2, 1, 2),
(11, '¿Cuántos jugadores tiene un equipo de fútbol?', 41, 42, 43, 44, 28, 23, 3),
(12, '¿Quién ganó el Mundial 2022?', 45, 46, 47, 48, 1, 0, 3),
(13, '¿En qué deporte se usa raqueta?', 49, 50, 51, 52, 1, 0, 3),
(14, '¿Cuántos sets tiene un Grand Slam masculino?', 53, 54, 55, 56, 1, 0, 3),
(15, '¿Qué deporte practica Messi?', 57, 58, 59, 60, 2, 1, 3),
(16, '¿Quién pintó la Mona Lisa?', 61, 62, 63, 64, 2, 0, 4),
(17, '¿En qué movimiento participó Picasso?', 65, 66, 67, 68, 1, 0, 4),
(18, '¿Quién pintó La noche estrellada?', 69, 70, 71, 72, 1, 0, 4),
(19, '¿Cuál es una obra de Leonardo da Vinci?', 73, 74, 75, 76, 4, 2, 4),
(20, '¿Qué estilo usa formas geométricas?', 77, 78, 79, 80, 4, 2, 4),
(21, '¿Cuál es el planeta más cercano al Sol?', 81, 82, 83, 84, 11, 9, 5),
(22, '¿Qué gas respiramos principalmente?', 85, 86, 87, 88, 1, 0, 5),
(23, '¿Cuántos estados de la materia clásica existen?', 89, 90, 91, 92, 1, 0, 5),
(24, '¿Qué órgano bombea la sangre?', 93, 94, 95, 96, 15, 11, 5),
(25, '¿Unidad de fuerza?', 97, 98, 99, 100, 1, 0, 5),
(26, '¿Quién creó Mickey Mouse?', 101, 102, 103, 104, 4, 1, 6),
(27, '¿Saga de Harry Potter?', 105, 106, 107, 108, 1, 0, 6),
(28, '¿Empresa creadora de PlayStation?', 109, 110, 111, 112, 2, 1, 6),
(29, '¿Juego más vendido de la historia?', 113, 114, 115, 116, 6, 4, 6),
(30, '¿Plataforma de streaming?', 117, 118, 119, 120, 3, 1, 6),
(31, '¿Qué tratado puso fin a la Guerra de los Treinta Años?', 121, 122, 123, 124, 10, 3, 1),
(32, '¿Quién fue el último emperador del Imperio Bizantino?', 125, 126, 127, 128, 11, 3, 1),
(33, '¿Cuál es la capital de Kazajistán?', 129, 130, 131, 132, 11, 3, 2),
(34, '¿Qué país posee la mayor parte de la selva amazónica?', 133, 134, 135, 136, 11, 3, 2),
(35, '¿En qué ciudad se celebraron los Juegos Olímpicos de 1992?', 137, 138, 139, 140, 10, 3, 3),
(36, '¿Qué tenista ganó más títulos de Roland Garros?', 141, 142, 143, 144, 10, 3, 3),
(37, '¿Quién pintó Las Meninas?', 145, 146, 147, 148, 11, 3, 4),
(38, '¿A qué movimiento pertenecía Salvador Dalí?', 149, 150, 151, 152, 10, 3, 4),
(39, '¿Qué científico propuso las leyes del movimiento planetario?', 153, 154, 155, 156, 10, 3, 5),
(40, '¿Cuál es el elemento químico con símbolo W?', 157, 158, 159, 160, 10, 3, 5),
(41, '¿Qué actor interpretó a Iron Man en el UCM?', 161, 162, 163, 164, 11, 3, 6),
(42, '¿Cuál fue la primera consola de Nintendo lanzada internacionalmente?', 165, 166, 167, 168, 10, 3, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_reportadas`
--

CREATE TABLE `preguntas_reportadas` (
  `id` int(11) NOT NULL,
  `motivo` text NOT NULL DEFAULT 'Sin motivo',
  `id_pregunta` int(11) NOT NULL,
  `mail_usuario` varchar(50) NOT NULL,
  `id_estado` int(50) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas_reportadas`
--

INSERT INTO `preguntas_reportadas` (`id`, `motivo`, `id_pregunta`, `mail_usuario`, `id_estado`) VALUES
(7, '                Es malisima', 20, 'testy@mail.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id` int(11) NOT NULL,
  `respuesta` varchar(60) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id`, `respuesta`, `es_correcta`) VALUES
(1, '1939', 1),
(2, '1914', 0),
(3, '1945', 0),
(4, '1960', 0),
(5, 'Julio Argentino Roca', 1),
(6, 'Domingo Sarmiento', 0),
(7, 'Manuel Belgrano', 0),
(8, 'José de San Martín', 0),
(9, 'Egipcios', 1),
(10, 'Romanos', 0),
(11, 'Griegos', 0),
(12, 'Persas', 0),
(13, '476 d.C.', 1),
(14, '1492 d.C.', 0),
(15, '1066 d.C.', 0),
(16, '300 d.C.', 0),
(17, 'Cristóbal Colón', 1),
(18, 'Américo Vespucio', 0),
(19, 'Fernando de Magallanes', 0),
(20, 'Hernán Cortés', 0),
(21, 'Amazonas', 1),
(22, 'Nilo', 0),
(23, 'Yangtsé', 0),
(24, 'Misisipi', 0),
(25, 'Canberra', 1),
(26, 'Sídney', 0),
(27, 'Melbourne', 0),
(28, 'Brisbane', 0),
(29, 'África', 1),
(30, 'Asia', 0),
(31, 'Europa', 0),
(32, 'América', 0),
(33, 'Rusia', 1),
(34, 'China', 0),
(35, 'Canadá', 0),
(36, 'EE.UU.', 0),
(37, 'Océano Pacífico', 1),
(38, 'Atlántico', 0),
(39, 'Índico', 0),
(40, 'Ártico', 0),
(41, '11', 1),
(42, '10', 0),
(43, '9', 0),
(44, '12', 0),
(45, 'Argentina', 1),
(46, 'Francia', 0),
(47, 'Brasil', 0),
(48, 'Alemania', 0),
(49, 'Tenis', 1),
(50, 'Fútbol', 0),
(51, 'Básquet', 0),
(52, 'Golf', 0),
(53, '5 sets', 1),
(54, '3 sets', 0),
(55, '7 sets', 0),
(56, '1 set', 0),
(57, 'Fútbol', 1),
(58, 'Tenis', 0),
(59, 'Rugby', 0),
(60, 'Béisbol', 0),
(61, 'Leonardo da Vinci', 1),
(62, 'Picasso', 0),
(63, 'Van Gogh', 0),
(64, 'Michelangelo', 0),
(65, 'Cubismo', 1),
(66, 'Impresionismo', 0),
(67, 'Barroco', 0),
(68, 'Renacimiento', 0),
(69, 'Van Gogh', 1),
(70, 'Picasso', 0),
(71, 'Dalí', 0),
(72, 'Monet', 0),
(73, 'La Mona Lisa', 1),
(74, 'La última cena', 0),
(75, 'El grito', 0),
(76, 'La persistencia de la memoria', 0),
(77, 'Cubismo', 1),
(78, 'Realismo', 0),
(79, 'Romanticismo', 0),
(80, 'Barroco', 0),
(81, 'Mercurio', 1),
(82, 'Venus', 0),
(83, 'Tierra', 0),
(84, 'Marte', 0),
(85, 'Oxígeno', 1),
(86, 'Nitrógeno', 0),
(87, 'Dióxido de carbono', 0),
(88, 'Hidrógeno', 0),
(89, '3', 1),
(90, '4', 0),
(91, '5', 0),
(92, '2', 0),
(93, 'Corazón', 1),
(94, 'Pulmón', 0),
(95, 'Cerebro', 0),
(96, 'Hígado', 0),
(97, 'Newton', 1),
(98, 'Joule', 0),
(99, 'Watt', 0),
(100, 'Pascal', 0),
(101, 'Walt Disney', 1),
(102, 'Pixar', 0),
(103, 'Warner Bros', 0),
(104, 'Marvel', 0),
(105, 'Harry Potter', 1),
(106, 'El Señor de los Anillos', 0),
(107, 'Star Wars', 0),
(108, 'Narnia', 0),
(109, 'Sony', 1),
(110, 'Microsoft', 0),
(111, 'Nintendo', 0),
(112, 'Sega', 0),
(113, 'Minecraft', 1),
(114, 'Tetris', 0),
(115, 'GTA V', 0),
(116, 'Fortnite', 0),
(117, 'Netflix', 1),
(118, 'YouTube', 0),
(119, 'Amazon', 0),
(120, 'Disney+', 0),
(121, 'Paz de Westfalia', 1),
(122, 'Tratado de Versalles', 0),
(123, 'Congreso de Viena', 0),
(124, 'Paz de Augsburgo', 0),
(125, 'Constantino XI', 1),
(126, 'Justiniano I', 0),
(127, 'Basilio II', 0),
(128, 'Alejo I', 0),
(129, 'Astaná', 1),
(130, 'Almatý', 0),
(131, 'Taskent', 0),
(132, 'Bakú', 0),
(133, 'Brasil', 1),
(134, 'Perú', 0),
(135, 'Colombia', 0),
(136, 'Venezuela', 0),
(137, 'Barcelona', 1),
(138, 'Madrid', 0),
(139, 'Atenas', 0),
(140, 'Seúl', 0),
(141, 'Rafael Nadal', 1),
(142, 'Roger Federer', 0),
(143, 'Novak Djokovic', 0),
(144, 'Björn Borg', 0),
(145, 'Diego Velázquez', 1),
(146, 'Goya', 0),
(147, 'El Greco', 0),
(148, 'Murillo', 0),
(149, 'Surrealismo', 1),
(150, 'Cubismo', 0),
(151, 'Impresionismo', 0),
(152, 'Expresionismo', 0),
(153, 'Johannes Kepler', 1),
(154, 'Isaac Newton', 0),
(155, 'Galileo Galilei', 0),
(156, 'Tycho Brahe', 0),
(157, 'Wolframio', 1),
(158, 'Vanadio', 0),
(159, 'Molibdeno', 0),
(160, 'Titanio', 0),
(161, 'Robert Downey Jr.', 1),
(162, 'Chris Evans', 0),
(163, 'Mark Ruffalo', 0),
(164, 'Chris Hemsworth', 0),
(165, 'NES', 1),
(166, 'Nintendo 64', 0),
(167, 'GameCube', 0),
(168, 'SNES', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_pregunta`
--

CREATE TABLE `tipos_pregunta` (
  `id` int(11) NOT NULL,
  `tipo` varchar(60) NOT NULL,
  `color` varchar(60) NOT NULL DEFAULT '#3B82F6'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_pregunta`
--

INSERT INTO `tipos_pregunta` (`id`, `tipo`, `color`) VALUES
(1, 'Historia', '#3B82F6'),
(2, 'Geografia', '#10B981'),
(3, 'Deportes', '#8B5CF6'),
(4, 'Arte', '#EF4444'),
(5, 'Ciencia', '#F5C700'),
(6, 'Entretenimiento', '#F57400');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_usuario`
--

CREATE TABLE `tipos_usuario` (
  `id` int(11) NOT NULL,
  `tipo` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_usuario`
--

INSERT INTO `tipos_usuario` (`id`, `tipo`) VALUES
(1, 'Comun'),
(2, 'Editor'),
(3, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `anio_nacimiento` int(11) NOT NULL,
  `sexo` enum('M','F','O') NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_validado` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `puntaje_max` int(11) NOT NULL DEFAULT 0,
  `preguntas_respondidas` int(11) NOT NULL DEFAULT 0,
  `preguntas_correctas` int(11) NOT NULL DEFAULT 0,
  `id_tipo` int(11) NOT NULL DEFAULT 1,
  `coins` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `anio_nacimiento`, `sexo`, `ciudad`, `pais`, `email`, `email_validado`, `password`, `usuario`, `foto_perfil`, `fecha_registro`, `puntaje_max`, `preguntas_respondidas`, `preguntas_correctas`, `id_tipo`, `coins`) VALUES
(16, 'Testy', 'Testerson', 1234, 'M', 'Banfield', 'Argentina', 'testy@mail.com', 1, '$2y$10$2JGqVwzZZnMlxrl54zrHJ.AOTGLkzVRpOuePEYqmflVhiadxGh05q', 'Testy Testerson', NULL, '2026-06-17 16:52:40', 6, 158, 95, 1, 372),
(18, 'Valentina', 'Gomez', 1998, '', 'San Justo', 'Argentina', 'valen@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'valeng', 'avatar2.jpg', '2026-02-15 14:20:00', 38, 95, 80, 2, 20),
(19, 'Kevin', 'Rodriguez', 1995, '', 'Laferrere', 'Argentina', 'kevin@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kevin_gym', 'avatar3.jpg', '2026-03-01 12:15:00', 50, 200, 185, 3, 100),
(20, 'Agustina', 'Perez', 1999, '', 'Moron', 'Argentina', 'agus@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agusp', 'avatar4.jpg', '2026-03-10 17:30:00', 25, 60, 45, 1, 10),
(21, 'Carlos', 'Lopez', 1994, '', 'CABA', 'Argentina', 'carlos@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlitos', 'avatar5.jpg', '2026-03-20 19:45:00', 12, 40, 20, 2, 5),
(22, 'Sofia', 'Martinez', 2000, '', 'La Plata', 'Argentina', 'sofi@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sofimart', 'avatar6.jpg', '2026-04-05 11:00:00', 42, 110, 95, 3, 40),
(24, 'Lucia', 'Sanchez', 1997, '', 'Cordoba', 'Argentina', 'lucia@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lu_sanchez', 'avatar8.jpg', '2026-04-18 22:00:00', 20, 50, 35, 2, 8),
(25, 'Matias', 'Gimenez', 1996, '', 'Mendoza', 'Argentina', 'mati@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mati_g', 'avatar9.jpg', '2026-05-02 13:20:00', 35, 90, 75, 3, 30),
(26, 'Camila', 'Torres', 1998, '', 'Tucuman', 'Argentina', 'cami@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cami_t', 'avatar10.jpg', '2026-05-10 14:35:00', 28, 70, 55, 1, 12),
(27, 'Lucas', 'Diaz', 1995, '', 'Salta', 'Argentina', 'lucas@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lucas_d', 'avatar11.jpg', '2026-05-15 12:50:00', 18, 45, 30, 2, 6),
(28, 'Florencia', 'Ruiz', 1997, '', 'Parana', 'Argentina', 'flor@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'flor_ruiz', 'avatar12.jpg', '2026-05-20 18:15:00', 40, 105, 90, 3, 35),
(29, 'Javier', 'Lopez', 1994, '', 'Santa Fe', 'Argentina', 'javier@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'javilop', 'avatar13.jpg', '2026-05-25 20:00:00', 22, 55, 40, 1, 9),
(30, 'Paula', 'Moreno', 1999, '', 'Neuquen', 'Argentina', 'paula@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paula_m', 'avatar14.jpg', '2026-06-01 13:00:00', 33, 85, 70, 2, 25),
(31, 'Tomas', 'Silva', 1996, '', 'Chubut', 'Argentina', 'tomi@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tomi_s', 'avatar15.jpg', '2026-06-05 16:20:00', 26, 65, 50, 3, 14),
(32, 'Brenda', 'Castro', 2000, '', 'Rio Negro', 'Argentina', 'brenda@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bren_c', 'avatar16.jpg', '2026-06-10 14:10:00', 15, 35, 25, 1, 4),
(33, 'Ezequiel', 'Ortiz', 1993, '', 'San Luis', 'Argentina', 'eze@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eze_o', 'avatar17.jpg', '2026-06-12 12:45:00', 31, 80, 65, 2, 22),
(34, 'Julieta', 'Acosta', 1998, '', 'Formosa', 'Argentina', 'juli@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juli_a', 'avatar18.jpg', '2026-06-15 17:05:00', 19, 48, 38, 3, 7),
(35, 'Rodrigo', 'Vazquez', 1995, '', 'Corrientes', 'Argentina', 'rodri@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rodri_v', 'avatar19.jpg', '2026-06-20 19:30:00', 36, 92, 78, 1, 28),
(36, 'Mariana', 'Diaz', 1997, '', 'La Rioja', 'Argentina', 'mari@mail.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mari_d', 'avatar20.jpg', '2026-06-25 13:00:00', 24, 60, 48, 2, 11);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `compras_simuladas`
--
ALTER TABLE `compras_simuladas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo` (`id_tipo_pregunta`),
  ADD KEY `respuesta1` (`id_respuesta1`),
  ADD KEY `respuesta2` (`id_respuesta2`),
  ADD KEY `respuesta3` (`id_respuesta3`),
  ADD KEY `respuesta4` (`id_respuesta4`);

--
-- Indices de la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_pregunta`
--
ALTER TABLE `tipos_pregunta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `tipo_usuario` (`id_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `compras_simuladas`
--
ALTER TABLE `compras_simuladas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT de la tabla `tipos_pregunta`
--
ALTER TABLE `tipos_pregunta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `respuesta1` FOREIGN KEY (`id_respuesta1`) REFERENCES `respuestas` (`id`),
  ADD CONSTRAINT `respuesta2` FOREIGN KEY (`id_respuesta2`) REFERENCES `respuestas` (`id`),
  ADD CONSTRAINT `respuesta3` FOREIGN KEY (`id_respuesta3`) REFERENCES `respuestas` (`id`),
  ADD CONSTRAINT `respuesta4` FOREIGN KEY (`id_respuesta4`) REFERENCES `respuestas` (`id`),
  ADD CONSTRAINT `tipo` FOREIGN KEY (`id_tipo_pregunta`) REFERENCES `tipos_pregunta` (`id`);

--
-- Filtros para la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
  ADD CONSTRAINT `fk_id_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `tipo_usuario` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
