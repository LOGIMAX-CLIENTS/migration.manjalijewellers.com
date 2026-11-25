-- Adds Notification Report to the menu, right after Send Notification.
-- The parent and position come from the Send Notification row because menu IDs differ between databases.

-- UP
INSERT INTO `menu` (`label`, `link`, `parent`, `sort`, `icon`, `active`)
SELECT 'Notification Report', 'reports/notification_report', s.`parent`, s.`sort` + 1, 'fa fa-circle-o', 1
FROM `menu` s
WHERE s.`link` = 'send/sendnotification'
  AND NOT EXISTS (SELECT 1 FROM `menu` m WHERE m.`link` = 'reports/notification_report')
LIMIT 1;

-- DOWN
DELETE FROM `menu` WHERE `link` = 'reports/notification_report';
