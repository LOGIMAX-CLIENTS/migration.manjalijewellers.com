-- Migration to add dynamic tag format settings
-- Author: NAMBI MUTHU RAJA
-- Date: 2026-03-28

-- UP
INSERT INTO ret_settings (name, value, description, created_on, created_by) 
SELECT 'tag_format', '2', 'Tag Format: 0-Seq, 1-Short, 2-Alpha', NOW(), 1 
WHERE NOT EXISTS (SELECT 1 FROM ret_settings WHERE name = 'tag_format');

INSERT INTO ret_settings (name, value, description, created_on, created_by) 
SELECT 'tag_digit_map', 'LJFWCXHGTN', 'Mapping for Alpha Tags (10 chars)', NOW(), 1 
WHERE NOT EXISTS (SELECT 1 FROM ret_settings WHERE name = 'tag_digit_map');
