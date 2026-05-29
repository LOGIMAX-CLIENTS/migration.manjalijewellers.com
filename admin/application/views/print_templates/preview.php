<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Preview</title>
    <style>
        @page {
            size: <?php echo $paper_size == 'A4' ? 'A4' : 'auto'; ?>;
            margin: 0;
        }
        body { margin: 0; padding: 0; }
        /* Add some basic resets */
    </style>
</head>
<body>
    <?php echo $html; ?>
    
    <script>
        // Auto print prompt
        // window.print();
    </script>
</body>
</html>
