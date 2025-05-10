<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 0;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <h1 class="error-code">500</h1>
            <p class="error-message">Internal Server Error</p>
            <p class="text-muted">Something went wrong on our servers. We're working to fix the issue.</p>
            <a href="/BATU_E_commerce/" class="btn btn-primary mt-3">Go to Homepage</a>
        </div>
    </div>
</body>
</html>