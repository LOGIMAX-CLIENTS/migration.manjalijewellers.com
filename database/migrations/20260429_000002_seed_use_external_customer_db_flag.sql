-- Migration: Seed USE_EXTERNAL_CUSTOMER_DB feature flag
-- Run once per client database.
-- Sets status=0 (OFF) by default — enable via Admin > Settings > Feature Flags UI.

UPDATE chit_settings
SET feature_flags = JSON_ARRAY_APPEND(
    IFNULL(feature_flags, '[]'), '$',
    JSON_OBJECT(
        'code',        'USE_EXTERNAL_CUSTOMER_DB',
        'description', 'Fetch customer records from the shared external database (same RDS instance)',
        'status',      0
    )
)
WHERE id_chit_settings = 1
  AND JSON_SEARCH(feature_flags, 'one', 'USE_EXTERNAL_CUSTOMER_DB') IS NULL;
