-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3307
-- Tiempo de generación: 12-06-2026 a las 02:11:32
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
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id` int(11) NOT NULL,
  `pregunta` varchar(60) NOT NULL,
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
(1, '¿En qué año comenzó la Segunda Guerra Mundial?', 1, 2, 3, 4, 0, 0, 1),
(2, '¿Quién fue el primer presidente de Argentina?', 5, 6, 7, 8, 0, 0, 1),
(3, '¿Qué civilización construyó las pirámides de Giza?', 9, 10, 11, 12, 0, 0, 1),
(4, '¿En qué año cayó el Imperio Romano de Occidente?', 13, 14, 15, 16, 0, 0, 1),
(5, '¿Quién descubrió América en 1492?', 17, 18, 19, 20, 0, 0, 1),
(6, '¿Cuál es el río más largo del mundo?', 21, 22, 23, 24, 0, 0, 2),
(7, '¿Cuál es la capital de Australia?', 25, 26, 27, 28, 0, 0, 2),
(8, '¿En qué continente está Egipto?', 29, 30, 31, 32, 0, 0, 2),
(9, '¿Cuál es el país más grande del mundo?', 33, 34, 35, 36, 0, 0, 2),
(10, '¿Cuál es el océano más grande?', 37, 38, 39, 40, 0, 0, 2),
(11, '¿Cuántos jugadores tiene un equipo de fútbol?', 41, 42, 43, 44, 0, 0, 3),
(12, '¿Quién ganó el Mundial 2022?', 45, 46, 47, 48, 0, 0, 3),
(13, '¿En qué deporte se usa raqueta?', 49, 50, 51, 52, 0, 0, 3),
(14, '¿Cuántos sets tiene un Grand Slam masculino?', 53, 54, 55, 56, 0, 0, 3),
(15, '¿Qué deporte practica Messi?', 57, 58, 59, 60, 0, 0, 3),
(16, '¿Quién pintó la Mona Lisa?', 61, 62, 63, 64, 0, 0, 4),
(17, '¿En qué movimiento participó Picasso?', 65, 66, 67, 68, 0, 0, 4),
(18, '¿Quién pintó La noche estrellada?', 69, 70, 71, 72, 0, 0, 4),
(19, '¿Cuál es una obra de Leonardo da Vinci?', 73, 74, 75, 76, 0, 0, 4),
(20, '¿Qué estilo usa formas geométricas?', 77, 78, 79, 80, 0, 0, 4),
(21, '¿Cuál es el planeta más cercano al Sol?', 81, 82, 83, 84, 0, 0, 5),
(22, '¿Qué gas respiramos principalmente?', 85, 86, 87, 88, 0, 0, 5),
(23, '¿Cuántos estados de la materia clásica existen?', 89, 90, 91, 92, 0, 0, 5),
(24, '¿Qué órgano bombea la sangre?', 93, 94, 95, 96, 0, 0, 5),
(25, '¿Unidad de fuerza?', 97, 98, 99, 100, 0, 0, 5),
(26, '¿Quién creó Mickey Mouse?', 101, 102, 103, 104, 0, 0, 6),
(27, '¿Saga de Harry Potter?', 105, 106, 107, 108, 0, 0, 6),
(28, '¿Empresa creadora de PlayStation?', 109, 110, 111, 112, 0, 0, 6),
(29, '¿Juego más vendido de la historia?', 113, 114, 115, 116, 0, 0, 6),
(30, '¿Plataforma de streaming?', 117, 118, 119, 120, 0, 0, 6);

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
(120, 'Disney+', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_pregunta`
--

CREATE TABLE `tipos_pregunta` (
  `id` int(11) NOT NULL,
  `tipo` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_pregunta`
--

INSERT INTO `tipos_pregunta` (`id`, `tipo`) VALUES
(1, 'Historia'),
(2, 'Geografia'),
(3, 'Deportes'),
(4, 'Arte'),
(5, 'Ciencia'),
(6, 'Entretenimiento');

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
  `id_tipo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `respuesta1` FOREIGN KEY (`id_respuesta1`) REFERENCES `respuestas` (`Id`),
  ADD CONSTRAINT `respuesta2` FOREIGN KEY (`id_respuesta2`) REFERENCES `respuestas` (`Id`),
  ADD CONSTRAINT `respuesta3` FOREIGN KEY (`id_respuesta3`) REFERENCES `respuestas` (`Id`),
  ADD CONSTRAINT `respuesta4` FOREIGN KEY (`id_respuesta4`) REFERENCES `respuestas` (`Id`),
  ADD CONSTRAINT `tipo` FOREIGN KEY (`id_tipo_pregunta`) REFERENCES `tipos_pregunta` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `tipo_usuario` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
