CREATE TABLE IF NOT EXISTS `ret_cash_adjustment` (
  `id_cash_adj`   INT(11) NOT NULL AUTO_INCREMENT,
  `id_branch`     INT(11) NOT NULL,
  `amount`        DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `adj_type`      TINYINT(1) NOT NULL COMMENT '1=Credit, 2=Debit',
  `narration`     VARCHAR(500) DEFAULT NULL,
  `adj_date`      DATE NOT NULL,
  `status`        TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 2=Cancelled',
  `created_by`    INT(11) NOT NULL,
  `created_at`    DATETIME NOT NULL,
  `cancelled_by`  INT(11) DEFAULT NULL,
  `cancelled_at`  DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_cash_adj`),
  KEY `idx_branch_date` (`id_branch`, `adj_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
