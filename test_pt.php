<?php
// Test what get_default(0) actually returns by simulating the CI DB query
$db = new PDO('mysql:host=localhost;dbname=arc_staging_24_02_26', 'root', '');

// Simulate: WHERE template_category=0 AND is_active=1 AND id_branch IS NULL 
// ORDER BY is_default DESC, id_branch DESC LIMIT 1
$row = $db->query("
    SELECT id_template, template_name, is_active, is_default, id_branch, LENGTH(template_css) as css_len 
    FROM print_templates 
    WHERE template_category = 0 
    AND is_active = 1 
    AND id_branch IS NULL
    ORDER BY is_default DESC, id_branch DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

echo "get_default(0) returns:\n";
print_r($row);
