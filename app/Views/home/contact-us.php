<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - BookSync</title>
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

        /* For the navbar button links */
        .navbar .nav-link {
            color: #495057 !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }

        /* Hover and focus states for Login button (outline button) */
        .navbar .nav-item .btn-outline-primary:hover,
        .navbar .nav-item .btn-outline-primary:focus {
            color: #ffffff !important;
            /* Change text color to white on hover/focus */
            background-color: var(--primary-color) !important;
            /* Change background on hover/focus */
        }

        /* Custom styles for Create Account button (primary button) */
        .navbar .nav-item .btn-primary {
            color: #ffffff !important;
            /* Keep text color white */
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-light) 0%, #ffffff 100%);
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-shape {
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 300px;
            height: 300px;
            background-color: rgba(84, 105, 212, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-shape-2 {
            position: absolute;
            top: -50px;
            left: 10%;
            width: 150px;
            height: 150px;
            background-color: rgba(84, 105, 212, 0.05);
            border-radius: 50%;
            z-index: 0;
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

        .contact-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .contact-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #dee2e6;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(84, 105, 212, 0.25);
        }

        .form-floating > label {
            padding: 0.75rem 1rem;
        }

        .form-floating > .form-control {
            height: auto;
            min-height: calc(3.5rem + 2px);
        }

        .form-floating > .form-control-textarea {
            min-height: 150px;
        }

        .map-container {
            height: 350px;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .faq-item {
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .faq-question {
            background-color: white;
            padding: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color);
            cursor: pointer;
            position: relative;
            border: none;
            width: 100%;
            text-align: left;
            transition: var(--transition);
        }

        .faq-question:hover {
            background-color: var(--primary-light);
        }

        .faq-answer {
            padding: 0 1.25rem;
            background-color: white;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-answer.active {
            padding: 1.25rem;
            max-height: 300px;
        }

        .faq-question::after {
            content: '\f078';
            font-family: 'Font Awesome 5 Free';
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            transition: var(--transition);
        }

        .faq-question.active::after {
            transform: translateY(-50%) rotate(180deg);
        }

        footer {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 4rem 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">BookSync</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link btn btn-outline-primary" href="/login">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn btn-primary text-white" href="/register">Create Account</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="hero-shape"></div>
        <div class="hero-shape-2"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">Get in <span class="text-primary">Touch</span></h1>
                    <p class="lead">Have questions or feedback about BookSync? We're here to help you with any inquiries about our digital library platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Us</h4>
                        <p class="text-muted">Our friendly team is here to help.</p>
                        <a href="mailto:codemonkeys@booksync.com" class="text-primary fw-bold">codemonkeys@booksync.com</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h4>Call Us</h4>
                        <p class="text-muted">Mon-Fri from 8am to 5pm.</p>
                        <a href="tel:+15551234567" class="text-primary fw-bold">+63 917 123 4567</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Visit Us</h4>
                        <p class="text-muted">Come say hello at our school.</p>
                        <address class="text-primary fw-bold mb-0">
                            CTU Main, R. Palma St., Cebu City
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">Send Us a Message</h2>
                    <p class="text-muted mb-4">Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <form id="contactForm" action="process_contact.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                <label for="subject">Subject</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-floating">
                                <textarea class="form-control form-control-textarea" id="message" name="message" placeholder="Your Message" style="height: 150px" required></textarea>
                                <label for="message">Your Message</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="privacyPolicy" required>
                            <label class="form-check-label text-muted" for="privacyPolicy">
                                I agree to the <a href="#" class="text-primary">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
                
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Our Location</h2>
                    <p class="text-muted mb-4">Visit our school in CTU Main.</p>
                    
                    <div class="map-container mb-4">
                        <!-- Placeholder for map (in a real implementation, you'd use Google Maps or similar) -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.5562851223526!2d123.90447977491375!3d10.297290167848868!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a999af5413e3b3%3A0xf2c6cfd993ed4e3d!2sCTU%20Main%20-%20R.%20Palma%20St.%20Gate!5e0!3m2!1sen!2sph!4v1744351213724!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-4">
                            <h5 class="fw-bold">Hours</h5>
                            <p class="text-muted mb-0">Monday - Friday: 9AM - 5PM<br>Saturday & Sunday: Closed</p>
                        </div>
                        <div>
                            <h5 class="fw-bold">Contact</h5>
                            <p class="text-muted mb-0">Phone: +63 917 123 4567<br>Email: codemonkeys@booksync.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-4">Stay Updated</h2>
                    <p class="mb-4">Subscribe to our newsletter for updates on new books, features, and special offers.</p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form class="d-flex">
                                <input type="email" class="form-control me-2" placeholder="Your email address" required>
                                <button type="submit" class="btn btn-light">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                    <a href="#" class="text-decoration-none text-muted me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="View our privacy policy">Privacy Policy</a>
                    <a href="#" class="text-decoration-none text-muted me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Read our terms of service">Terms of Service</a>
                    <a href="#" class="text-decoration-none text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="You are here">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>