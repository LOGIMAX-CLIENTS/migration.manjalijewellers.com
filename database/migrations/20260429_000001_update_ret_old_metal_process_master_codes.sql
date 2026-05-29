-- Migration: Update process_code values in ret_old_metal_process_master
-- Author: NAMBI MUTHU RAJA
-- Date: 2026-04-29

UPDATE ret_old_metal_process_master SET process_code = 'MELTING' WHERE ret_old_metal_process_master.id_metal_process = 1;

UPDATE ret_old_metal_process_master SET process_code = 'TESTING' WHERE ret_old_metal_process_master.id_metal_process = 2;

UPDATE ret_old_metal_process_master SET process_code = 'REFINING' WHERE ret_old_metal_process_master.id_metal_process = 3;
