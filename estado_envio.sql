-- ============================================================
-- ESTADO DE ENVÍO PARA EL PANEL LOGÍSTICO DE STORE DANY
-- Ejecutar en DOS sitios:
--   1) MariaDB local (XAMPP/Docker)        -> base: gst_ventasonline
--   2) InfinityFree (phpMyAdmin)           -> base: if0_41988386_gst_ventasonline
-- ============================================================

ALTER TABLE pedidos
    ADD COLUMN estado_envio ENUM('pendiente','empacado','enviado') NOT NULL DEFAULT 'pendiente' AFTER total,
    ADD COLUMN fecha_estado DATETIME NULL DEFAULT NULL AFTER estado_envio;

-- El panel admin.php ya hace el UPDATE automático con los botones
-- 🕒 Pendiente → 📦 Empacado → 🚚 Enviado
-- (nueva columna + fecha del último cambio en fecha_estado)