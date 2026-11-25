-- Gives each profile the same view access to Notification Report that it has to Send Notification.
-- Must run after 20261125_000002_add_notification_report_menu.sql.

-- UP
INSERT INTO `access` (`id_profile`, `id_menu`, `view`, `add`, `edit`, `delete`)
SELECT a.`id_profile`, n.`id_menu`, MAX(a.`view`), 0, 0, 0
FROM `access` a
JOIN `menu` src ON src.`id_menu` = a.`id_menu` AND src.`link` = 'send/sendnotification'
JOIN `menu` n ON n.`link` = 'reports/notification_report'
WHERE NOT EXISTS (
  SELECT 1 FROM `access` x WHERE x.`id_profile` = a.`id_profile` AND x.`id_menu` = n.`id_menu`
)
GROUP BY a.`id_profile`, n.`id_menu`;

-- DOWN
DELETE a FROM `access` a
JOIN `menu` n ON n.`id_menu` = a.`id_menu`
WHERE n.`link` = 'reports/notification_report';
