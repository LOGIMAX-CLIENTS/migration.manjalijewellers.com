<?php
$mysqli = new mysqli("localhost", "root", "root", "retaillogimaxind_test_etail_v4");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$id = 45;
$res = $mysqli->query("SELECT id_template, template_name, template_category, gjs_data, template_html, template_css FROM print_templates WHERE id_template = $id");
$row = $res->fetch_assoc();

if ($row) {
    file_put_contents('template_45_debug_gjs.json', $row['gjs_data']);
    file_put_contents('template_45_debug_html.html', $row['template_html']);
    file_put_contents('template_45_debug_css.css', $row['template_css']);
    echo "Success: Template data saved.\n";
} else {
    echo "Error: Template not found.\n";
}
$mysqli->close();
?>
