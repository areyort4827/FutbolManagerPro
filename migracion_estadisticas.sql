-- =====================================================
-- MIGRACIÓN: Tablas de estadísticas por partido
-- Ejecutar una sola vez en la base de datos
-- =====================================================

-- Tabla de estadísticas individuales por partido
CREATE TABLE IF NOT EXISTS `estadisticas_jugador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jugador_id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `goles` int(11) NOT NULL DEFAULT 0,
  `asistencias` int(11) NOT NULL DEFAULT 0,
  `minutos_jugados` int(11) NOT NULL DEFAULT 0,
  `tarjetas_amarillas` tinyint(1) NOT NULL DEFAULT 0,
  `tarjetas_rojas` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jugador_partido` (`jugador_id`, `partido_id`),
  CONSTRAINT `estadisticas_jugador_ibfk_1` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estadisticas_jugador_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de goles por partido (usada también en estadísticas globales)
CREATE TABLE IF NOT EXISTS `goles_partido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partido_id` int(11) NOT NULL,
  `jugador_id` int(11) NOT NULL,
  `cantidad_goles` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `goles_partido_ibfk_1` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goles_partido_ibfk_2` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
