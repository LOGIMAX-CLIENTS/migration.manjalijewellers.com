<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 30px;
        }

        .btn {
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
            padding: 12px;
        }

        .btn:hover {
            background-color: #007bff;
            color: white;
        }

        .card {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .card-body a {
            text-decoration: none;
        }

        .card-body a:hover {
            text-decoration: underline;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand {
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Control Panel</a>
        </div>
    </nav>

    <div class="container">
        <h1>Database Migration</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Choose Action</h5>
                <a href="./differentiate_tables.php" class="btn btn-outline-primary">Tables Comparison</a>
                <a href="./differentiate_columns.php" class="btn btn-outline-primary">Column Comparison</a>
                <a href="./insert_datas.php" class="btn btn-outline-primary">Insert Data</a>
                <a href="./size_comparision.php" class="btn btn-outline-primary">Size Comparision</a>
            </div>
        </div>
    </div>

</body>

</html>