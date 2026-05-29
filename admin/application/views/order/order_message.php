<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Logimax</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --success: #22c55e;
            --success-bg: #f0fdf4;
            --success-border: #bbf7d0;
            --danger: #ef4444;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --info: #3b82f6;
            --info-bg: #eff6ff;
            --info-border: #bfdbfe;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--text-main);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message-box {
            padding: 60px 40px;
            text-align: center;
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 30px;
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .icon.success {
            color: var(--success);
        }

        .icon.danger {
            color: var(--danger);
        }

        .icon.info {
            color: var(--info);
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0 0 15px 0;
            color: var(--text-main);
        }

        p {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin: 0;
        }

        .badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 30px;
            font-size: 0.9rem;
        }

        .badge.success {
            background: var(--success-bg);
            color: var(--success);
            border: 2px solid var(--success-border);
        }

        .badge.danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 2px solid var(--danger-border);
        }

        .badge.info {
            background: var(--info-bg);
            color: var(--info);
            border: 2px solid var(--info-border);
        }

        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-box">
            <?php if($type == 'success'): ?>
                <div class="icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            <?php elseif($type == 'danger'): ?>
                <div class="icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
            <?php else: ?>
                <div class="icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
            <?php endif; ?>

            <h1><?php echo $title; ?></h1>
            <p><?php echo $message; ?></p>

            <div class="badge <?php echo $type; ?>">
                <?php 
                    if($type == 'success') echo 'Already Processed';
                    elseif($type == 'danger') echo 'Link Expired';
                    else echo 'Information';
                ?>
            </div>
        </div>
    </div>
</body>
</html>
