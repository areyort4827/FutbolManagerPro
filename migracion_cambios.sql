-- ============================================================
-- MIGRACIÓN: FutbolManagerPro - 4 mejoras
-- Ejecutar en phpMyAdmin o MySQL Workbench
-- ============================================================

-- 1. JUGADORES: añadir fecha_nacimiento y soft-delete
ALTER TABLE `jugadores`
  ADD COLUMN `fecha_nacimiento` DATE NULL AFTER `nombre`,
  ADD COLUMN `eliminado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `equipo_id`,
  ADD COLUMN `equipo_anterior_id` INT(11) NULL AFTER `eliminado`;

-- Rellenar fecha_nacimiento aproximada a partir de la edad actual (para datos ya existentes)
UPDATE `jugadores` SET `fecha_nacimiento` = DATE_SUB(CURDATE(), INTERVAL `edad` YEAR) WHERE `edad` IS NOT NULL;

-- 2. ESTADÍSTICAS: tabla dinámica por partido
CREATE TABLE IF NOT EXISTS `estadisticas_jugador` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jugador_id` INT(11) NOT NULL,
  `partido_id` INT(11) NOT NULL,
  `goles` INT(11) NOT NULL DEFAULT 0,
  `asistencias` INT(11) NOT NULL DEFAULT 0,
  `minutos_jugados` INT(11) NOT NULL DEFAULT 0,
  `tarjetas_amarillas` TINYINT(1) NOT NULL DEFAULT 0,
  `tarjetas_rojas` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jugador_partido` (`jugador_id`, `partido_id`),
  FOREIGN KEY (`jugador_id`) REFERENCES `jugadores`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`partido_id`) REFERENCES `partidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. PARTIDOS: añadir hora y equipo_id del club para poder filtrar por club en el calendario
ALTER TABLE `partidos`
  ADD COLUMN `hora` TIME NULL AFTER `fecha`,
  ADD COLUMN `club_id` INT(11) NULL AFTER `hora`;

-- Índice para búsquedas rápidas por club y fecha
CREATE INDEX `idx_partidos_club_fecha` ON `partidos`(`club_id`, `fecha`);
