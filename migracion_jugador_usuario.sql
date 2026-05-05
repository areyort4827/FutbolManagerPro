-- ===================================================================
-- Migración: vinculación jugador ↔ usuario para el rol "jugador"
-- Ejecutar una sola vez sobre la BD futbolmanagerpro
-- ===================================================================

-- 1. Añadir columna usuario_id a jugadores (si no existe ya)
ALTER TABLE `jugadores`
  ADD COLUMN IF NOT EXISTS `usuario_id` INT(11) DEFAULT NULL
  AFTER `equipo_anterior_id`;

-- 2. Índice y FK (omitir si ya existe)
ALTER TABLE `jugadores`
  ADD KEY IF NOT EXISTS `fk_jugador_usuario` (`usuario_id`);

ALTER TABLE `jugadores`
  ADD CONSTRAINT IF NOT EXISTS `fk_jugador_usuario`
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Ejemplo: vincular un usuario con rol 'jugador' a su jugador
--    Sustituye los IDs según tu BD.
--    UPDATE jugadores SET usuario_id = <id_usuario> WHERE id = <id_jugador>;
