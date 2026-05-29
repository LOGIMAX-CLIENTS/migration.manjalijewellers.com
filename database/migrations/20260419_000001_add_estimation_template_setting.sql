-- Migration: Add estimation_template_based setting to ret_settings
-- This controls whether the estimation module uses the template engine (1) or legacy DOMPDF (0)

INSERT INTO `ret_settings` (`name`, `value`)
SELECT 'estimation_template_based', '0'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'estimation_template_based'
);
