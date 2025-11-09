<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        <?= htmlspecialchars($status) ?>
        — <?= htmlspecialchars($message ?: 'An unexpected error occurred') ?>
    </title>
    <style>
        :root {
            color-scheme: light dark;
        }

        body {
            background: #f8fafc;
            color: #1e293b;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .container {
            text-align: center;
            max-width: 500px;
            padding: 24px;
        }

        h1 {
            font-size: 52px;
            margin-bottom: 10px;
            color: #007bff;
            /* Codemonster blue */
        }

        h2 {
            font-size: 22px;
            color: #334155;
            /* slate-700 */
            margin-bottom: 12px;
        }

        p {
            color: #64748b;
            /* slate-500 */
            font-size: 16px;
        }

        footer {
            margin-top: 24px;
            color: #94a3b8;
            /* slate-400 */
            font-size: 13px;
        }

        /* 🌙 Dark theme */
        @media (prefers-color-scheme: dark) {
            body {
                background: #0a192f;
                /* deep navy blue */
                color: #e2e8f0;
            }

            h1 {
                color: #339cff;
                /* lighter Codemonster blue */
            }

            h2 {
                color: #cbd5e1;
            }

            p {
                color: #94a3b8;
            }

            footer {
                color: #64748b;
            }
        }
    </style>
</head>

<body>
    <?php
    $defaultMessages = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        408 => 'Request Timeout',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
    ];

    $code = $status ?? 500;
    $messageText = $message
        ?: ($defaultMessages[$code] ?? 'An unexpected error occurred');
    ?>

    <div class="container">
        <h1><?= htmlspecialchars($code) ?></h1>
        <h2><?= htmlspecialchars($messageText) ?></h2>
        <p>Sorry, something went wrong while processing your request.</p>
        <footer>Codemonster Annabel</footer>
    </div>
</body>

</html>