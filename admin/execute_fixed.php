<?php
$mysqli = new mysqli("localhost", "root", "root", "retaillogimaxind_test_etail_v4");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$gjs_data = file_get_contents('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_gjs.json');
$template_html = file_get_contents('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_html.html');
$template_css = file_get_contents('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_css.css');

$stmt = $mysqli->prepare("UPDATE print_templates SET gjs_data = ?, template_html = ?, template_css = ? WHERE id_template = 45");
$stmt->bind_param("sss", $gjs_data, $template_html, $template_css);

if ($stmt->execute()) {
    echo "Success: Template 45 layout fixed and updated successfully.\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
$mysqli->close();
?>
