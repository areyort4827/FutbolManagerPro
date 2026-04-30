-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-04-2026 a las 14:03:40
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
-- Base de datos: `futbolmanagerpro3`
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
(1, 'Emerson Cruz', 2, 1, 1),
(2, 'Carlo Ancelotti', 9, 6, 4);

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
(4, 2, 'Sesión táctica', 'Transiciones defensa-ataque', '2026-04-20', '00:00:00', 0, 3, NULL, 6),
(5, 2, 'Sesión táctica', 'Jugadas a balón parado', '2026-04-21', '00:00:00', 0, 3, NULL, 6),
(7, 3, 'Sesión táctica', 'Presión en bloque medio', '2026-04-20', '00:00:00', 0, 0, NULL, 11),
(8, 3, 'Sesión táctica', 'Basculaciones defensivas', '2026-04-21', '00:00:00', 0, 0, NULL, 11),
(9, 3, 'Sesión táctica', 'Contragolpe explosivo', '2026-04-22', '00:00:00', 0, 5, NULL, 11),
(11, 4, 'Sesión táctica', 'Salida de balón', '2026-04-21', '00:00:00', 0, 0, NULL, 14),
(12, 4, 'Sesión táctica', 'Resistencia física', '2026-04-22', '00:00:00', 0, 0, NULL, 14),
(14, 1, 'Sesión de físico', 'Preparacion', '2026-04-12', '17:55:00', 110, 0, 'Écija', 5),
(15, 1, 'Sesión de físico', 'jajaja', '2026-04-20', '23:00:00', 200, 2, 'La Campana', 1),
(18, 1, 'Sesión técnica', '', '2026-04-30', '10:48:00', 30, 0, 'Pabellon alcarrachela', 1);

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
(6, 4, 6, 1),
(7, 7, 7, 1);

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
(1, 1, 'Barcelona', 'Senior'),
(2, 1, 'Barcelona B', 'Filial'),
(3, 1, 'Barcelona Juvenil', 'Juvenil'),
(4, 1, 'Barcelona Cadete', 'Cadete'),
(5, 1, 'Barcelona Infantil', 'Infantil'),
(6, 2, 'Real Madrid', 'Senior'),
(7, 2, 'Real Madrid Castilla', 'Filial'),
(8, 2, 'Real Madrid Juvenil', 'Juvenil'),
(9, 2, 'Real Madrid Cadete', 'Cadete'),
(10, 2, 'Real Madrid Infantil', 'Infantil'),
(11, 3, 'Atlético de Madrid', 'Senior'),
(12, 3, 'Atlético Juvenil', 'Juvenil'),
(13, 3, 'Atlético Cadete', 'Cadete'),
(14, 4, 'Valencia CF', 'Senior'),
(15, 4, 'Valencia Juvenil', 'Juvenil'),
(16, 4, 'Valencia Cadete', 'Cadete');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_jugador`
--

CREATE TABLE `estadisticas_jugador` (
  `id` int(11) NOT NULL,
  `jugador_id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `goles` int(11) NOT NULL DEFAULT 0,
  `asistencias` int(11) NOT NULL DEFAULT 0,
  `minutos_jugados` int(11) NOT NULL DEFAULT 0,
  `tarjetas_amarillas` tinyint(1) NOT NULL DEFAULT 0,
  `tarjetas_rojas` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `goles_partido`
--

CREATE TABLE `goles_partido` (
  `id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `jugador_id` int(11) NOT NULL,
  `cantidad_goles` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `goles_partido`
--

INSERT INTO `goles_partido` (`id`, `partido_id`, `jugador_id`, `cantidad_goles`) VALUES
(9, 57, 1, 2),
(12, 61, 1, 2),
(13, 61, 3, 1),
(14, 62, 25, 2),
(15, 63, 25, 1),
(16, 63, 26, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `posicion` enum('delantero','mediocentro','defensa','portero') DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `equipo_anterior_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `nombre`, `fecha_nacimiento`, `edad`, `posicion`, `equipo_id`, `eliminado`, `equipo_anterior_id`) VALUES
(1, 'Lamine Yamal', '2008-04-27', 18, 'delantero', 1, 0, NULL),
(2, 'Robert Lewandowski', '1989-04-27', 37, 'delantero', NULL, 1, 1),
(3, 'Pedri', '2002-06-13', 23, 'mediocentro', 1, 0, NULL),
(4, 'Vinícius Júnior', '2001-04-27', 25, 'delantero', 6, 0, NULL),
(6, 'Fede Valverde', '2001-07-28', 24, 'mediocentro', 6, 0, NULL),
(7, 'Antoine Griezmann', '1991-04-27', 35, 'delantero', 11, 0, NULL),
(10, 'Pepelu', '1999-04-27', 27, 'delantero', NULL, 1, 14),
(11, 'Hugo Duro', '2000-04-27', 26, 'delantero', NULL, 1, 14),
(12, 'José Gayà', '1996-04-27', 30, 'mediocentro', NULL, 1, 14),
(13, 'Diego Kochen', '2006-04-27', 20, 'portero', NULL, 1, 9),
(15, 'Mbappe', '1999-04-27', 27, 'delantero', 6, 0, NULL),
(19, 'Antonio Reyes', '2010-07-14', NULL, 'delantero', NULL, 1, 3),
(20, 'Raphina', '2000-07-14', NULL, 'delantero', 1, 0, NULL),
(21, 'Álvaro Rodríguez', '2007-06-14', NULL, 'delantero', 1, 0, NULL),
(22, 'Antonio Reyes', '2003-07-14', 22, 'delantero', NULL, 1, 2),
(23, 'Antonio Reyes', '2003-07-29', NULL, 'defensa', NULL, 1, 1),
(24, 'Raphina', '2006-07-13', 19, 'delantero', 6, 0, NULL),
(25, 'Emerson Cruz', '2006-04-06', NULL, 'defensa', 2, 0, NULL),
(26, 'Cristiano Ronaldo', '2026-04-30', NULL, 'portero', 2, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `equipo_local` varchar(100) DEFAULT NULL,
  `equipo_visitante` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `resultado` varchar(20) DEFAULT NULL,
  `equipo_local_id` int(11) DEFAULT NULL,
  `equipo_visitante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `equipo_local`, `equipo_visitante`, `fecha`, `hora`, `club_id`, `resultado`, `equipo_local_id`, `equipo_visitante_id`) VALUES
(52, NULL, NULL, '2026-05-10', NULL, NULL, NULL, 1, 6),
(57, NULL, NULL, '2026-03-30', NULL, NULL, '2-2', 1, 15),
(61, NULL, NULL, '2026-03-30', NULL, NULL, '3-1', 1, 8),
(62, NULL, NULL, '2026-03-30', NULL, NULL, '2-2', 1, 2),
(63, NULL, NULL, '2026-04-18', NULL, NULL, '2-2', 1, 2);

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
(1, 'Emerson Cruz', 'emerson@gmail.com', '$2y$10$NJI4oL/xSs0VQisfXnSuwe13NO/McOfq/7eifqCI/0RlQLfvfwfsC', 'entrenador', 1),
(2, 'Admin', 'admin@gmail.com', '$2y$10$5np9QVsD6B8x/1at6tiOXu.rJ6DCVDprAFMQRZsmEcnRme2LoJD1y', 'admin', NULL),
(3, 'Barcelona', 'barcelona@gmail.com', '$2y$10$bUJ3.Cm9DqdgZ6vhS3EBs.9waJUw2/ho97AiEKCog2bJrdN97B/bS', 'equipo', 1),
(4, 'Carlo Ancelotti', 'madrid@gmail.com', '$2y$10$HSIQ0u6FnvNKLCudzEWUx.ffkZfderZfyIic6fGcyBz3w9IH3tNcq', 'entrenador', 2),
(5, 'Real Madrid CF', 'madrid2@gmail.com', '$2y$10$zlb4yXY9nX15d0LwEgI5Y.vjZ2wapQRHKCewRpK7TtSVzIj2NLnyy', 'equipo', 2);

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
-- Indices de la tabla `estadisticas_jugador`
--
ALTER TABLE `estadisticas_jugador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_jugador_partido` (`jugador_id`,`partido_id`),
  ADD KEY `partido_id` (`partido_id`);

--
-- Indices de la tabla `goles_partido`
--
ALTER TABLE `goles_partido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partido_id` (`partido_id`),
  ADD KEY `jugador_id` (`jugador_id`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jugadores_id_cantera` (`equipo_id`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partidos_club_fecha` (`club_id`,`fecha`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- AUTO_INCREMENT de la tabla `estadisticas_jugador`
--
ALTER TABLE `estadisticas_jugador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `goles_partido`
--
ALTER TABLE `goles_partido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Filtros para la tabla `estadisticas_jugador`
--
ALTER TABLE `estadisticas_jugador`
  ADD CONSTRAINT `estadisticas_jugador_ibfk_1` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estadisticas_jugador_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `goles_partido`
--
ALTER TABLE `goles_partido`
  ADD CONSTRAINT `goles_partido_ibfk_1` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goles_partido_ibfk_2` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `jugadores_id_cantera` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `equipo_id_usuarios` FOREIGN KEY (`club_id`) REFERENCES `clubes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
