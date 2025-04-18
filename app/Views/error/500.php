<!-- 500.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | BookSync</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #5469d4;
            --primary-light: #e6ebff;
            --secondary-color: #1a2236;
            --accent-color: #4caf93;
            --light-gray: #f8f9fa;
            --border-radius: 0.5rem;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            box-shadow: var(--box-shadow);
            background-color: white !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--secondary-color) !important;
        }

        .navbar .nav-link {
            color: #495057 !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }

        .navbar .nav-item .btn-outline-primary:hover,
        .navbar .nav-item .btn-outline-primary:focus {
            color: #ffffff !important;
            background-color: var(--primary-color) !important;
        }

        .navbar .nav-item .btn-primary {
            color: #ffffff !important;
        }

        .btn {
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #4258c5;
            border-color: #4258c5;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .error-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: var(--secondary-color);
        }

        .error-details {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .error-image {
            max-width: 300px;
            margin-bottom: 2rem;
        }

        footer {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }

        @media (max-width: 576px) {
            .error-code {
                font-size: 6rem;
            }

            .error-message {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="/">BookSync</a>
        </div>
    </nav>

    <!-- Error Content -->
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-message">Server Error</h1>
        <p class="error-details"><?= htmlspecialchars($message ?? "Something went wrong on our end. We're working to fix it.") ?></p>
        
        <!-- Replaced image with Font Awesome icon -->
        <div class="error-icon mb-4">
            <i class="fas fa-gears fa-5x text-danger"></i>
        </div>
        
        <div class="d-flex gap-3">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <a href="#" class="btn btn-outline-primary">Contact Support</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-3 bg-white border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="fw-bold fs-5 me-2">BookSync</span>
                        <span class="text-muted">© 2025 All rights reserved</span>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-decoration-none text-muted me-3">Privacy Policy</a>
                    <a href="#" class="text-decoration-none text-muted me-3">Terms of Service</a>
                    <a href="/contact-us" class="text-decoration-none text-muted">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>