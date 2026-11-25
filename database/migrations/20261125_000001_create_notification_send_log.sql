-- Notification Report: one row per device per push send, written by Fcm_service::sendToTokens.
-- batch_ref is one per send operation (one admin send, one broadcast, or one scheduled run); with
-- the title it groups the rows of one push for the "notification wise" report view.
-- device_ref holds only the last 8 characters of the token, so the report never exposes the full
-- FCM token. is_sent = 1 means FCM accepted the message, not that the phone received it.

-- UP
CREATE TABLE IF NOT EXISTS `notification_send_log` (
  `id_log` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `batch_ref` VARCHAR(32) NOT NULL,
  `id_customer` INT UNSIGNED NULL,
  `device_ref` VARCHAR(20) NOT NULL,
  `device_type` TINYINT UNSIGNED NULL COMMENT '1 Android, 2 iOS, as in registered_devices.device_type',
  `noti_title` TEXT NULL,
  `noti_body` TEXT NULL,
  `sent_at` DATETIME NOT NULL,
  `is_sent` TINYINT(1) NOT NULL,
  `fcm_message_id` VARCHAR(255) NULL,
  `error_code` VARCHAR(100) NULL,
  PRIMARY KEY (`id_log`),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_batch_ref` (`batch_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DOWN
DROP TABLE IF EXISTS `notification_send_log`;
