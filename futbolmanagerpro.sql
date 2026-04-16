-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2026 a las 09:31:11
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
CREATE DATABASE IF NOT EXISTS `futbolmanagerpro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `futbolmanagerpro`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubes`
--

DROP TABLE IF EXISTS `clubes`;
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

DROP TABLE IF EXISTS `entrenadores`;
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
(1, 'Alejandro Quezada', 2, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

DROP TABLE IF EXISTS `entrenamientos`;
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
(1, 1, 'Sesión de físico', 'Trabajo con balón y precisión', '2025-12-23', '17:00:00', 90, 0, 'Campo B', 2),
(3, 2, 'Sesión técnica', 'Partido de práctica', '2026-03-25', '12:00:00', 120, 0, 'Campo A', 4),
(4, 2, 'Sesión táctica', '', '2026-03-25', '10:00:00', 30, 0, 'Campo A', 5),
(21, 1, 'Sesión de físico', '', '2026-03-25', '12:04:00', 11, 0, 'Pabellon alcarrachela', 10),
(22, 2, 'Sesión táctica', '', '2026-03-02', '14:04:00', 2, 0, 'Pabellon alcarrachela', 5),
(23, 2, 'Sesión de físico', '', '2026-03-26', '12:34:00', 12, 0, 'Pabellon alcarrachela', 3),
(26, 2, 'Sesión táctica', '', '2026-04-07', '15:11:00', 35, 0, 'Gimnasio', 3),
(27, 2, 'Sesión táctica', '', '2026-04-07', '15:13:00', 35, 0, 'Pabellon alcarrachela', 3),
(31, 2, 'Sesión técnica', NULL, '2026-01-14', '17:00:00', 30, 0, 'Pabellon ', 9),
(32, 2, 'Sesión de físico', '', '2026-04-15', '15:00:00', 90, 11, 'Aqui', 5),
(33, 2, 'Sesión táctica', '', '2026-04-15', '11:20:00', 30, 10, 'Pabellon alcarrachela', 9),
(79, 1, 'Sesión táctica', 'Preparación del Clásico contra Real Madrid', '2026-04-28', '17:30:00', 95, 23, 'Ciudad Deportiva Joan Gamper', 2),
(80, 1, 'Sesión de físico', 'Trabajo de fuerza y prevención de lesiones', '2026-04-29', '09:15:00', 70, 20, 'Ciudad Deportiva Joan Gamper', 2),
(81, 1, 'Sesión técnica', 'Posesión y transiciones rápidas', '2026-04-30', '18:00:00', 85, 18, 'Campo 3 - Gamper', 10),
(82, 1, 'Sesión pre-partido', 'Activación y estrategia final', '2026-05-02', '10:30:00', 60, 16, 'Ciudad Deportiva Joan Gamper', 2),
(83, 2, 'Sesión táctica', 'Análisis del derbi contra Atlético', '2026-04-28', '18:30:00', 100, 24, 'Valdebebas', 5),
(84, 2, 'Sesión de físico', 'Velocidad y explosividad', '2026-04-29', '08:45:00', 80, 21, 'Valdebebas', 5),
(85, 2, 'Sesión técnica', 'Trabajo de finalización', '2026-05-01', '17:30:00', 90, 23, 'Valdebebas', 6),
(86, 2, 'Sesión pre-partido', 'Preparación contra Barcelona', '2026-05-03', '10:00:00', 65, 25, 'Valdebebas', 5),
(87, 3, 'Sesión técnica', 'Énfasis en presión alta', '2026-04-29', '17:00:00', 90, 21, 'Centro Deportivo Wanda', 16),
(88, 3, 'Sesión táctica', 'Defensa de bloques bajos', '2026-04-30', '16:45:00', 75, 23, 'Centro Deportivo Wanda', 16),
(89, 4, 'Sesión de físico', 'Trabajo específico de resistencia', '2026-04-30', '19:00:00', 85, 5, 'Ciudad Deportiva Valencia', 19),
(90, 4, 'Sesión táctica', 'Preparación contra Sevilla', '2026-05-02', '18:00:00', 90, 5, 'Ciudad Deportiva Valencia', 19),
(91, 2, 'Sesión táctica', '', '2026-04-17', '10:23:00', 23, 0, 'Pabellon alcarrachela', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamiento_asistencia`
--

DROP TABLE IF EXISTS `entrenamiento_asistencia`;
CREATE TABLE `entrenamiento_asistencia` (
  `id` int(11) NOT NULL,
  `entrenamiento_id` int(11) NOT NULL,
  `jugador_id` int(11) NOT NULL,
  `asistio` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

DROP TABLE IF EXISTS `equipos`;
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
(2, 1, 'Barcelona B', 'Senior'),
(3, 2, 'Real Madrid Castilla', 'Senior'),
(4, 2, 'Real Madrid A', 'Cadete'),
(5, 2, 'Real Madrid B', 'Cadete'),
(6, 2, 'Real Madrid A', 'Juvenil'),
(7, 2, 'Real Madrid A', 'Infantil'),
(8, 2, 'Real Madrid B', 'Juvenil'),
(9, 2, 'Real Madrid B', 'Infantil'),
(10, 1, 'Barcelona A', 'Juvenil'),
(11, 1, 'Barcelona A', 'Cadete'),
(12, 1, 'Barcelona A', 'Infantil'),
(13, 1, 'Barcelona B', 'Juvenil'),
(14, 1, 'Barcelona B', 'Cadete'),
(15, 1, 'Barcelona B', 'Infantil'),
(16, 3, 'Atlético de Madrid A', 'Senior'),
(17, 3, 'Atlético Juvenil A', 'Juvenil'),
(18, 3, 'Atlético Cadete A', 'Cadete'),
(19, 4, 'Valencia CF A', 'Senior'),
(20, 4, 'Valencia Mestalla', 'Juvenil'),
(21, 4, 'Valencia Cadete A', 'Cadete'),
(22, 1, 'Barcelona Juvenil B', 'Juvenil'),
(23, 2, 'Real Madrid Juvenil B', 'Juvenil'),
(24, 1, 'Barcelona Juvenil A', 'Juvenil'),
(25, 1, 'Barcelona Cadete A', 'Cadete'),
(26, 2, 'Real Madrid Juvenil A', 'Juvenil'),
(27, 2, 'Real Madrid Cadete A', 'Cadete'),
(28, 3, 'Atlético Juvenil A', 'Juvenil'),
(29, 3, 'Atlético Cadete A', 'Cadete'),
(30, 4, 'Valencia Juvenil A', 'Juvenil'),
(31, 1, 'Barcelona Juvenil A', 'Juvenil'),
(32, 1, 'Barcelona Cadete A', 'Cadete'),
(33, 2, 'Real Madrid Juvenil A', 'Juvenil'),
(34, 2, 'Real Madrid Cadete A', 'Cadete'),
(35, 3, 'Atlético Juvenil A', 'Juvenil'),
(36, 3, 'Atlético Cadete A', 'Cadete'),
(37, 4, 'Valencia Juvenil A', 'Juvenil'),
(38, 1, 'Barcelona Juvenil A', 'Juvenil'),
(39, 1, 'Barcelona Cadete A', 'Cadete'),
(40, 2, 'Real Madrid Juvenil A', 'Juvenil'),
(41, 2, 'Real Madrid Cadete A', 'Cadete'),
(42, 3, 'Atlético Juvenil A', 'Juvenil'),
(43, 3, 'Atlético Cadete A', 'Cadete'),
(44, 4, 'Valencia Juvenil A', 'Juvenil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

DROP TABLE IF EXISTS `jugadores`;
CREATE TABLE `jugadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `posicion` enum('delantero','mediocentro','defensa','portero') DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `nombre`, `edad`, `posicion`, `equipo_id`) VALUES
(28, 'Raphina', 28, 'delantero', 2),
(30, 'Iñaki Peña', 25, 'portero', 2),
(31, 'Ronald Araújo', 26, 'defensa', 2),
(32, 'Jules Koundé', 26, 'defensa', 2),
(33, 'Andreas Christensen', 28, 'defensa', 2),
(34, 'Alejandro Balde', 21, 'defensa', 2),
(35, 'Íñigo Martínez', 33, 'defensa', 2),
(36, 'Frenkie de Jong', 27, 'mediocentro', 2),
(37, 'Pedri', 22, 'mediocentro', 2),
(38, 'Gavi', 20, 'mediocentro', 2),
(39, 'Ilkay Gündogan', 34, 'mediocentro', 2),
(40, 'Oriol Romeu', 33, 'mediocentro', 2),
(41, 'Robert Lewandowski', 36, 'delantero', 2),
(42, 'Raphinha', 28, 'delantero', 2),
(44, 'Lamine Yamal', 17, 'delantero', 2),
(45, 'João Félix', 25, 'delantero', 2),
(46, 'Antonio Reyes', 22, 'delantero', 10),
(47, 'Raphina', 22, 'portero', 10),
(48, 'Antonio Reyes', 12, 'delantero', 15),
(50, 'Andriy Lunin', 25, 'portero', 3),
(51, 'Kepa Arrizabalaga', 29, 'portero', 3),
(52, 'Dani Carvajal', 32, 'defensa', 3),
(53, 'Éder Militão', 26, 'defensa', 3),
(54, 'David Alaba', 32, 'defensa', 3),
(55, 'Nacho Fernández', 34, 'defensa', 3),
(56, 'Antonio Rüdiger', 31, 'defensa', 3),
(57, 'Ferland Mendy', 29, 'defensa', 3),
(58, 'Fran García', 24, 'defensa', 3),
(59, 'Jude Bellingham', 21, 'mediocentro', 3),
(60, 'Luka Modric', 38, 'mediocentro', 3),
(61, 'Toni Kroos', 34, 'mediocentro', 3),
(62, 'Federico Valverde', 26, 'mediocentro', 3),
(63, 'Eduardo Camavinga', 21, 'mediocentro', 3),
(64, 'Aurélien Tchouaméni', 24, 'mediocentro', 3),
(65, 'Dani Ceballos', 28, 'mediocentro', 3),
(66, 'Vinícius Jr.', 24, 'delantero', 3),
(67, 'Rodrygo Goes', 23, 'delantero', 3),
(68, 'Brahim Díaz', 25, 'delantero', 3),
(69, 'Joselu Mato', 34, 'delantero', 3),
(70, 'Nico Paz', 20, 'mediocentro', 6),
(71, 'Álvaro Rodríguez', 25, 'defensa', 6),
(74, 'Manuel Ángel', 20, 'portero', 7),
(75, 'Raphina', 22, 'delantero', 2),
(76, 'Lucas Cañizares', 29, 'delantero', 3),
(77, 'Raphina', 5, 'mediocentro', 3),
(80, 'Juan', 5, 'portero', 3),
(82, '33', 3, 'delantero', 3),
(84, 'Antonio Reyes', 23, 'portero', 7),
(85, 'Antonio Rüdiger', 33, 'defensa', 5),
(86, 'Aurélien Tchouaméni', 26, 'mediocentro', 5),
(87, 'Rodrygo Goes', 25, 'delantero', 5),
(88, 'Brahim Díaz', 26, 'delantero', 5),
(89, 'Jan Oblak', 33, 'portero', 16),
(90, 'Koke Resurrección', 34, 'mediocentro', 16),
(91, 'Álvaro Morata', 33, 'delantero', 16),
(92, 'Giorgi Mamardashvili', 24, 'portero', 19),
(93, 'Javi Guerra', 21, 'mediocentro', 19),
(99, 'Antonio Rüdiger', 33, 'defensa', 5),
(100, 'Aurélien Tchouaméni', 26, 'mediocentro', 5),
(101, 'Rodrygo Goes', 25, 'delantero', 5),
(102, 'Brahim Díaz', 26, 'delantero', 5),
(103, 'Jan Oblak', 33, 'portero', 16),
(104, 'Koke Resurrección', 34, 'mediocentro', 16),
(105, 'Álvaro Morata', 33, 'delantero', 16),
(106, 'Giorgi Mamardashvili', 24, 'portero', 19),
(107, 'Javi Guerra', 21, 'mediocentro', 19),
(108, 'Hugo Duro', 26, 'delantero', 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

DROP TABLE IF EXISTS `partidos`;
CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `equipo_local` varchar(100) DEFAULT NULL,
  `equipo_visitante` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `resultado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `equipo_local`, `equipo_visitante`, `fecha`, `resultado`) VALUES
(1, 'Real Madrid', 'Barcelona', '2026-04-06', '20-0'),
(2, 'Sevilla', 'Valencia', '2026-04-12', '2-2'),
(3, 'Betis', 'Atletico Madrid', '2026-04-15', '1-1'),
(4, 'Paris', 'Liverpool', '2026-04-11', '3-2'),
(5, 'Juventus', 'Marsella', '2026-04-17', '1-3'),
(6, 'Real Madrid', 'FC Barcelona', '2026-04-15', '2-1'),
(12, 'Real Madrid', 'FC Barcelona', '2026-04-13', '3-0'),
(13, 'Real Madrid', 'FC Barcelona', '2026-04-13', '2-0'),
(14, 'FC Barcelona', 'Real Madrid CF', '2026-04-26', '2-1'),
(15, 'Atlético de Madrid', 'Valencia CF', '2026-04-27', '3-0'),
(16, 'Real Madrid CF', 'Sevilla FC', '2026-05-03', '4-2'),
(17, 'FC Barcelona', 'Atlético de Madrid', '2026-05-10', '3-3'),
(18, 'Valencia CF', 'Sevilla FC', '2026-05-11', '1-1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
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
(1, 'admin', 'admin@gmail.com', 'admin123', 'admin', 2),
(2, 'entrenador', 'entrenador@gmail.com', 'entrenador123', 'entrenador', 1),
(3, 'jugador', 'jugador@gmail.com', 'jugador123', 'jugador', 1),
(4, 'equipo', 'equipo@gmail.com', 'equipo123', 'equipo', 2),
(5, 'equipo2', 'equipo2@gmail.com', 'equipo2', 'equipo', 1),
(6, 'Equipo Atlético', 'equipo@atleticomadrid.com', 'atleti123', 'equipo', 3),
(7, 'Equipo Valencia', 'equipo@valenciacf.com', 'valencia123', 'equipo', 4),
(8, 'Diego Simeone', 'simeone@atleticomadrid.com', 'cholo123', 'entrenador', 3),
(9, 'Rubén Baraja', 'baraja@valenciacf.com', 'baraja123', 'entrenador', 4),
(10, 'Xavi Hernández', 'xavi@fcbarcelona.com', 'xavi123', 'entrenador', 1),
(11, 'Carlo Ancelotti', 'ancelotti@realmadrid.com', 'carlo123', 'entrenador', 2),
(14, 'Equipo Barcelona', 'equipo@fcbarcelona.com', 'barca123', 'equipo', 1),
(15, 'Equipo Real Madrid', 'equipo@realmadrid.com', 'madrid123', 'equipo', 2);

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
  ADD KEY `jugadores_id_cantera` (`equipo_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD CONSTRAINT `entrenadores_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `clubes` (`id`),
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
