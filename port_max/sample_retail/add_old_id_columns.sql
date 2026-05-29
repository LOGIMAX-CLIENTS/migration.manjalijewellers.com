-- =====================================================
-- Add old_id column to all master tables
-- For source system ID traceability during migration
-- =====================================================

-- Masters
ALTER TABLE ret_karigar ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_category ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_product_master ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_design_master ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_sub_design_master ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_section ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_stone ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_charges ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_purity ADD COLUMN old_id INT NULL COMMENT 'Source system ID';
ALTER TABLE ret_size ADD COLUMN old_id INT NULL COMMENT 'Source system ID';

-- Add index for quick lookups during tag import resolution
CREATE INDEX idx_old_id ON ret_karigar (old_id);
CREATE INDEX idx_old_id ON ret_category (old_id);
CREATE INDEX idx_old_id ON ret_product_master (old_id);
CREATE INDEX idx_old_id ON ret_design_master (old_id);
CREATE INDEX idx_old_id ON ret_sub_design_master (old_id);
CREATE INDEX idx_old_id ON ret_section (old_id);
CREATE INDEX idx_old_id ON ret_stone (old_id);
CREATE INDEX idx_old_id ON ret_charges (old_id);
CREATE INDEX idx_old_id ON ret_purity (old_id);
CREATE INDEX idx_old_id ON ret_size (old_id);
