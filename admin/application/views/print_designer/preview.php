<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview — <?php echo htmlspecialchars($template['name']); ?></title>
    <link href="<?php echo base_url(); ?>assets/print-designer/css/print.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <?php
    // Calculate page dimensions
    $paper_sizes = [
        'A4'     => [210, 297],
        'A3'     => [297, 420],
        'A5'     => [148, 210],
        'Letter' => [216, 279],
        'Legal'  => [216, 356],
        '80mm'   => [80, 297],
        '58mm'   => [58, 210],
    ];

    $page_size = $template['page_size'] ?: 'A4';
    $orientation = $template['orientation'] ?: 'portrait';

    if ($page_size === 'Custom' && $template['page_width_mm'] && $template['page_height_mm']) {
        $w_mm = (float) $template['page_width_mm'];
        $h_mm = (float) $template['page_height_mm'];
    } else {
        $dims = isset($paper_sizes[$page_size]) ? $paper_sizes[$page_size] : $paper_sizes['A4'];
        $w_mm = $dims[0];
        $h_mm = $dims[1];
    }

    if ($orientation === 'landscape') {
        $tmp = $w_mm; $w_mm = $h_mm; $h_mm = $tmp;
    }

    $mm_to_px = 3.7795275591;
    $canvas_w = round($w_mm * $mm_to_px);
    $canvas_h = round($h_mm * $mm_to_px);
    ?>

    <style>
        @page {
            size: <?php echo $w_mm; ?>mm <?php echo $h_mm; ?>mm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            font-family: 'Inter', Arial, sans-serif;
        }
        .pd-print-controls {
            font-family: 'Inter', sans-serif;
        }
        .pd-print-page {
            width: <?php echo $canvas_w; ?>px;
            height: <?php echo $canvas_h; ?>px;
            margin: 60px auto 30px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        @media print {
            .pd-print-controls { display: none !important; }
            body { background: #fff; }
            .pd-print-page {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="page-<?php echo $page_size; ?> <?php echo $orientation; ?>">

<!-- Control Bar (hidden on print) -->
<div class="pd-print-controls">
    <button class="pd-btn-print" onclick="window.print()">
        <i class="fa fa-print"></i> Print
    </button>
    <button class="pd-btn-close" onclick="window.close()">
        <i class="fa fa-times"></i> Close
    </button>
    <span style="color: #999; font-size: 13px; margin-left: 16px;">
        <?php echo htmlspecialchars($template['name']); ?> |
        <?php echo $page_size; ?> <?php echo ucfirst($orientation); ?> |
        Row ID: <?php echo $row_id ?: 'N/A'; ?>
    </span>
</div>

<!-- Print Page -->
<div class="pd-print-page">
<?php
if (!empty($template['layout_json'])) {
    $canvas_data = json_decode($template['layout_json'], true);
    $objects = isset($canvas_data['objects']) ? $canvas_data['objects'] : [];

    foreach ($objects as $obj) {
        $type = isset($obj['type']) ? $obj['type'] : '';
        $left = isset($obj['left']) ? $obj['left'] : 0;
        $top  = isset($obj['top']) ? $obj['top'] : 0;
        $scaleX = isset($obj['scaleX']) ? $obj['scaleX'] : 1;
        $scaleY = isset($obj['scaleY']) ? $obj['scaleY'] : 1;

        // Skip grid lines
        if (!empty($obj['isGrid'])) continue;

        if ($type === 'textbox' || $type === 'i-text' || $type === 'text') {
            $ow = (isset($obj['width']) ? $obj['width'] : 200) * $scaleX;
            $fontSize = isset($obj['fontSize']) ? $obj['fontSize'] : 14;
            $fontFamily = isset($obj['fontFamily']) ? $obj['fontFamily'] : 'Arial';
            $fontWeight = isset($obj['fontWeight']) ? $obj['fontWeight'] : 'normal';
            $fontStyle = isset($obj['fontStyle']) ? $obj['fontStyle'] : 'normal';
            $textAlign = isset($obj['textAlign']) ? $obj['textAlign'] : 'left';
            $color = isset($obj['fill']) ? $obj['fill'] : '#000';
            $underline = !empty($obj['underline']) ? 'text-decoration:underline;' : '';
            $angle = isset($obj['angle']) ? $obj['angle'] : 0;
            $transform = $angle ? "transform:rotate({$angle}deg);" : '';
            $text = isset($obj['text']) ? $obj['text'] : '';

            // Substitute variables
            if ($row_data) {
                $text = preg_replace_callback('/\{\{(\w+)\}\}/', function($m) use ($row_data) {
                    $col = $m[1];
                    return isset($row_data[$col]) ? htmlspecialchars($row_data[$col]) : $m[0];
                }, $text);
            } else {
                // Show placeholder labels
                $text = preg_replace_callback('/\{\{(\w+)\}\}/', function($m) {
                    $label = ucwords(str_replace('_', ' ', $m[1]));
                    return "[{$label}]";
                }, $text);
            }

            echo '<div class="pd-print-element pd-type-text" style="position:absolute;left:' . $left . 'px;top:' . $top . 'px;width:' . $ow . 'px;'
                . 'font-size:' . $fontSize . 'px;font-family:' . $fontFamily . ';font-weight:' . $fontWeight . ';'
                . 'font-style:' . $fontStyle . ';text-align:' . $textAlign . ';color:' . $color . ';'
                . $underline . $transform
                . 'white-space:pre-wrap;word-wrap:break-word;line-height:1.3;">'
                . nl2br(htmlspecialchars_decode($text))
                . '</div>';

        } elseif ($type === 'rect') {
            $rw = (isset($obj['width']) ? $obj['width'] : 100) * $scaleX;
            $rh = (isset($obj['height']) ? $obj['height'] : 100) * $scaleY;
            $fill = isset($obj['fill']) ? $obj['fill'] : 'transparent';
            $stroke = isset($obj['stroke']) ? $obj['stroke'] : '#000';
            $strokeW = isset($obj['strokeWidth']) ? $obj['strokeWidth'] : 1;
            $rx = isset($obj['rx']) ? $obj['rx'] : 0;

            echo '<div class="pd-print-element pd-type-rect" style="position:absolute;left:' . $left . 'px;top:' . $top . 'px;'
                . 'width:' . $rw . 'px;height:' . $rh . 'px;'
                . 'background:' . $fill . ';border:' . $strokeW . 'px solid ' . $stroke . ';'
                . ($rx ? 'border-radius:' . $rx . 'px;' : '')
                . '"></div>';

        } elseif ($type === 'line') {
            $x1 = isset($obj['x1']) ? $obj['x1'] : 0;
            $x2 = isset($obj['x2']) ? $obj['x2'] : 200;
            $stroke = isset($obj['stroke']) ? $obj['stroke'] : '#000';
            $strokeW = isset($obj['strokeWidth']) ? $obj['strokeWidth'] : 1;
            $lw = abs($x2 - $x1) * $scaleX;

            echo '<div class="pd-print-element pd-type-line" style="position:absolute;left:' . $left . 'px;top:' . $top . 'px;'
                . 'width:' . $lw . 'px;height:0px;'
                . 'border-top:' . $strokeW . 'px solid ' . $stroke . ';'
                . '"></div>';
        }
    }
}
?>
</div>

<!-- Font Awesome for control bar -->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
</body>
</html>
