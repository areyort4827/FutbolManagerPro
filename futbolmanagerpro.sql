-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2026 a las 14:18:48
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
-- Base de datos: `futbolmanagerpro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubes`
--

CREATE TABLE `clubes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clubes`
--

INSERT INTO `clubes` (`id`, `nombre`) VALUES
(1, 'Barcelona'),
(2, 'Real Madrid'),
(3, 'Atlético de Madrid'),
(4, 'Valencia CF'),
(5, 'Sevilla FC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenadores`
--

CREATE TABLE `entrenadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `experiencia` int(11) DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entrenadores`
--

INSERT INTO `entrenadores` (`id`, `nombre`, `experiencia`, `equipo_id`, `usuario_id`) VALUES
(1, 'Emerson Cruz', 2, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `titulo` enum('Sesión táctica','Sesión técnica','Sesión pre-partido','Sesión de físico') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `duracion` int(11) NOT NULL,
  `num_asistentes` int(11) DEFAULT 0,
  `lugar` varchar(100) DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entrenamientos`
--

INSERT INTO `entrenamientos` (`id`, `club_id`, `titulo`, `descripcion`, `fecha`, `hora`, `duracion`, `num_asistentes`, `lugar`, `equipo_id`) VALUES
(2, 1, 'Sesión táctica', 'Táctica defensiva', '2026-04-21', '00:00:00', 0, 2, 'Tenerife', 1),
(4, 2, 'Sesión táctica', 'Transiciones defensa-ataque', '2026-04-20', '00:00:00', 0, 0, NULL, 6),
(5, 2, 'Sesión táctica', 'Jugadas a balón parado', '2026-04-21', '00:00:00', 0, 0, NULL, 6),
(7, 3, 'Sesión táctica', 'Presión en bloque medio', '2026-04-20', '00:00:00', 0, 0, NULL, 11),
(8, 3, 'Sesión táctica', 'Basculaciones defensivas', '2026-04-21', '00:00:00', 0, 0, NULL, 11),
(9, 3, 'Sesión táctica', 'Contragolpe explosivo', '2026-04-22', '00:00:00', 0, 5, NULL, 11),
(11, 4, 'Sesión táctica', 'Salida de balón', '2026-04-21', '00:00:00', 0, 0, NULL, 14),
(12, 4, 'Sesión táctica', 'Resistencia física', '2026-04-22', '00:00:00', 0, 0, NULL, 14),
(14, 1, 'Sesión de físico', 'Preparacion', '2026-04-12', '17:55:00', 110, 0, 'Écija', 5),
(15, 1, 'Sesión de físico', 'jajaja', '2026-04-24', '23:00:00', 200, 3, 'La Campana', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamiento_asistencia`
--

CREATE TABLE `entrenamiento_asistencia` (
  `id` int(11) NOT NULL,
  `entrenamiento_id` int(11) NOT NULL,
  `jugador_id` int(11) NOT NULL,
  `asistio` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entrenamiento_asistencia`
--

INSERT INTO `entrenamiento_asistencia` (`id`, `entrenamiento_id`, `jugador_id`, `asistio`) VALUES
(4, 4, 4, 1),
(5, 4, 5, 1),
(6, 4, 6, 1),
(7, 7, 7, 1),
(8, 7, 8, 1),
(9, 7, 9, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `equipo_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `equipo_id`, `nombre`, `categoria`) VALUES
(1, 1, 'Barcelona', 'Primer Equipo'),
(2, 1, 'Barcelona B', 'Filial'),
(3, 1, 'Barcelona Juvenil', 'Juvenil'),
(4, 1, 'Barcelona Cadete', 'Cadete'),
(5, 1, 'Barcelona Infantil', 'Infantil'),
(6, 2, 'Real Madrid', 'Primer Equipo'),
(7, 2, 'Real Madrid Castilla', 'Filial'),
(8, 2, 'Real Madrid Juvenil', 'Juvenil'),
(9, 2, 'Real Madrid Cadete', 'Cadete'),
(10, 2, 'Real Madrid Infantil', 'Infantil'),
(11, 3, 'Atlético de Madrid', 'Primer Equipo'),
(12, 3, 'Atlético Juvenil', 'Juvenil'),
(13, 3, 'Atlético Cadete', 'Cadete'),
(14, 4, 'Valencia CF', 'Primer Equipo'),
(15, 4, 'Valencia Juvenil', 'Juvenil'),
(16, 4, 'Valencia Cadete', 'Cadete');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `posicion` enum('delantero','mediocentro','defensa','portero') DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `nombre`, `edad`, `posicion`, `equipo_id`, `usuario_id`) VALUES
(1, 'Lamine Yamal', 18, 'delantero', 1, 4),
(2, 'Robert Lewandowski', 37, 'delantero', 1, NULL),
(3, 'Pedri', 23, 'mediocentro', 1, NULL),
(4, 'Vinícius Júnior', 25, 'delantero', 6, NULL),
(5, 'Jude Bellingham', 22, 'mediocentro', 6, NULL),
(6, 'Fede Valverde', 27, 'mediocentro', 6, NULL),
(7, 'Antoine Griezmann', 35, 'delantero', 11, NULL),
(8, 'Koke', 34, 'mediocentro', 11, NULL),
(9, 'Jan Oblak', 33, 'portero', 11, NULL),
(10, 'Pepelu', 27, 'delantero', 14, NULL),
(11, 'Hugo Duro', 26, 'delantero', 14, NULL),
(12, 'José Gayà', 30, 'mediocentro', 14, NULL),
(13, 'Diego Kochen', 20, 'portero', 2, NULL),
(15, 'Mbappe', 27, 'delantero', 6, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `equipo_local` varchar(100) DEFAULT NULL,
  `equipo_visitante` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `resultado` varchar(20) DEFAULT NULL,
  `equipo_local_id` int(11) DEFAULT NULL,
  `equipo_visitante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `equipo_local`, `equipo_visitante`, `fecha`, `resultado`, `equipo_local_id`, `equipo_visitante_id`) VALUES
(2, NULL, NULL, '2026-05-10', '1-1', 1, 11),
(3, NULL, NULL, '2026-05-20', '3-3', 1, 14),
(4, NULL, NULL, '2026-10-15', '3-3', 6, 1),
(20, NULL, NULL, '2026-04-21', '2-1', 1, 15),
(21, NULL, NULL, '2026-04-29', '2-2', 1, 2),
(22, NULL, NULL, '2026-04-30', '3-3', 15, 16),
(23, NULL, NULL, '2026-04-21', '10-10', 1, 15),
(24, NULL, NULL, '2026-04-21', '4-4', 3, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('admin','equipo','entrenador','jugador') DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `club_id`) VALUES
(1, 'Emerson Cruz', 'emersoncruz712@gmail.com', '$2y$10$xYdDL8.3k1Z2tKw5Tn.xI.9NbPQzQ1.yZLMqs1Kv8m0JOE7lCMcQa', 'entrenador', 1),
(2, 'Admin', 'admin@gmail.com', 'admin123', 'admin', NULL),
(3, 'Barcelona', 'barcelona@gmail.com', 'barcelona123', 'equipo', 1),
(4, 'Lamine Yamal', 'antonioreyesortega03@gmail.com\r\n', '1234', 'jugador', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clubes`
--
ALTER TABLE `clubes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id` (`equipo_id`),
  ADD KEY `fk_usuario_entrenador` (`usuario_id`);

--
-- Indices de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id` (`club_id`),
  ADD KEY `entrenamiento_equipo_id_fk` (`equipo_id`);

--
-- Indices de la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entrenamiento_id` (`entrenamiento_id`),
  ADD KEY `jugador_id` (`jugador_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id` (`equipo_id`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jugadores_id_cantera` (`equipo_id`),
  ADD KEY `fk_jugador_usuario` (`usuario_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id_usuarios` (`club_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clubes`
--
ALTER TABLE `clubes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD CONSTRAINT `fk_entrenador_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `fk_usuario_entrenador` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `entrenamiento_equipo_id_fk` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `entrenamientos_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  ADD CONSTRAINT `entrenamiento_asistencia_ibfk_1` FOREIGN KEY (`entrenamiento_id`) REFERENCES `entrenamientos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrenamiento_asistencia_ibfk_2` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `clubes` (`id`);

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `fk_jugador_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `jugadores_id_cantera` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `equipo_id_usuarios` FOREIGN KEY (`club_id`) REFERENCES `clubes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
