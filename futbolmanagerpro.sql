-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-03-2026 a las 12:21:39
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
(1, 'Alejandro Quezada', 2, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `titulo` enum('Sesión táctica','Sesión técnica','Sesión pre-partido','Sesión de físico') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `duracion` int(11) NOT NULL,
  `lugar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entrenamientos`
--

INSERT INTO `entrenamientos` (`id`, `equipo_id`, `titulo`, `descripcion`, `fecha`, `hora`, `duracion`, `lugar`) VALUES
(2, 1, 'Sesión técnica', 'Trabajo con balón y precisión', '2025-12-23', '17:00:00', 90, 'Campo B'),
(3, 1, 'Sesión de físico', 'Preparación física intensiva', '2025-12-21', '15:00:00', 120, 'Gimnasio'),
(18, 2, 'Sesión técnica', 'Partido de práctica', '2026-03-25', '12:00:00', 120, 'Campo A');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `categoria`) VALUES
(1, 'Barcelona B', 'Juvenil'),
(2, 'Real Madrid', 'Primer equipo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

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
(1, 'Antonio Reyes', 21, 'delantero', 1),
(2, 'Emerson Cruz', 19, 'defensa', 1),
(22, 'Vinicius', 22, 'delantero', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('admin','equipo','entrenador','jugador') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin123', 'admin'),
(2, 'entrenador', 'entrenador@gmail.com', 'entrenador123', 'entrenador'),
(3, 'jugador', 'jugador@gmail.com', 'jugador123', 'jugador'),
(4, 'equipo', 'equipo@gmail.com', 'equipo123', 'equipo');

--
-- Índices para tablas volcadas
--

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
  ADD KEY `equipo_id` (`equipo_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id` (`equipo_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  ADD CONSTRAINT `entrenadores_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `fk_usuario_entrenador` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `entrenamientos_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entrenamiento_asistencia`
--
ALTER TABLE `entrenamiento_asistencia`
  ADD CONSTRAINT `entrenamiento_asistencia_ibfk_1` FOREIGN KEY (`entrenamiento_id`) REFERENCES `entrenamientos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrenamiento_asistencia_ibfk_2` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `jugadores_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
