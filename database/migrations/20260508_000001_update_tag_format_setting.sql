-- Migration: Update tag_format setting from '2' to '1'
-- Date: 2026-05-08
-- Description: Change tag print format to default format for applicable clients

UPDATE ret_settings SET value = '1' WHERE name = 'tag_format' AND value = '2';
