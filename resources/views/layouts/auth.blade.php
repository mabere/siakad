<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOEFL Test Prep</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #007BFF;
            --secondary-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--secondary-color);
            color: var(--dark-color);
        }

        .navbar-nav .nav-link {
            color: #fff !important;
        }

        .hero {
            background-color: var(--primary-color);
            color: white;
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
        }

        .footer a {
            color: #ccc;
            text-decoration: none;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s;
        }
    </style>
</head>

<body>
    <x-custom.header />

    <x-custom.navbar />

    <main>
        {{ $slot }}
    </main>

    <x-custom.footer />

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>