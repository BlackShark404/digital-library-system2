<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSync - Your Digital Library Solution</title>
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

        html {
            scroll-behavior: smooth;
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


        .hero {
            background: linear-gradient(135deg, var(--primary-light) 0%, #ffffff 100%);
            padding: 6rem 0;
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

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .feature-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .feature-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-5px);
        }

        .feature-icon {
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

        .testimonial-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            position: relative;
        }

        .testimonial-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 3px solid white;
        }

        .pricing-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            text-align: center;
        }

        .pricing-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-5px);
        }

        .pricing-card.highlighted {
            border: 2px solid var(--primary-color);
            position: relative;
        }

        .flash-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 16px 24px;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.4s ease-out forwards;
        }

            .toast {
                margin-bottom: 25px;
            }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .highlight-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
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

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3a4db5 100%);
            padding: 5rem 0;
            color: white;
        }

        footer {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .book-preview {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .book-preview:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
        }

        .book-preview img {
            width: 100%;
            height: auto;
            transition: var(--transition);
        }

        .book-preview:hover img {
            transform: scale(1.05);
        }

        .book-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
        }

        .stats {
            padding: 4rem 0;
            background-color: var(--primary-light);
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .hero {
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
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-shape"></div>
        <div class="hero-shape-2"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4">Your Digital Library <span class="text-primary">Reimagined</span></h1>
                    <p class="lead mb-4">Experience the future of reading with BookSync. Access thousands of books, read online, and build your personal digital library - all in one place.</p>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-primary">Get Started</a>
                        <a href="#how-it-works" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Featured Books</h2>
                <p class="text-muted">Discover popular titles available on BookSync</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="book-preview">
                        <img src="/assets/images/book-cover/In Search of Lost Time Cover.jpg" alt="Book Cover" class="img-fluid">
                        <div class="book-overlay">
                            <h5>In Search of Lost Time</h5>
                            <p class="mb-0">By Marcel Proust</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="book-preview">
                        <img src="/assets/images/book-cover/To Kill a Mockingbird Cover.jpg" alt="Book Cover" class="img-fluid">
                        <div class="book-overlay">
                            <h5>To Kill a Mockingbird</h5>
                            <p class="mb-0">By Harper Lee</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="book-preview">
                        <img src="/assets/images/book-cover/The Great Gatsby Cover.jpg" alt="Book Cover" class="img-fluid">
                        <div class="book-overlay">
                            <h5>The Great Gatsby</h5>
                            <p class="mb-0">By F. Scott Fitzgerald</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="/login" class="btn btn-outline-primary">Browse All Books</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-value">10,000+</div>
                        <div class="stat-label">Digital Books</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-value">5,000+</div>
                        <div class="stat-label">Active Readers</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-value">100+</div>
                        <div class="stat-label">Categories</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value">95%</div>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose BookSync</h2>
                <p class="text-muted">Discover the benefits of our digital library system</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <h4>Try Before You Buy</h4>
                        <p class="text-muted">Get a 3-day free preview of any book before deciding to purchase. Read a sample to ensure it meets your needs.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Exclusive Reading</h4>
                        <p class="text-muted">Each book can be accessed by only 3 readers at a time, ensuring exclusive reading experiences and author support.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <h4>Own Your Purchases</h4>
                        <p class="text-muted">Purchase books to read indefinitely and download PDF versions for offline reading on your desktop.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h4>Personal Library</h4>
                        <p class="text-muted">Build and organize your digital collection with personal reading lists, favorites, and history tracking.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>Advanced Search</h4>
                        <p class="text-muted">Find exactly what you're looking for with our powerful search and filtering system across categories.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Secure Platform</h4>
                        <p class="text-muted">Your reading history and purchases are protected with our state-of-the-art security system.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-light" id="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How BookSync Works</h2>
                <p class="text-muted">Get started with our digital library in just a few steps</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                        <img src="/assets/images/landing-page/create-account.jpeg" alt="Create Account" class="img-fluid rounded-3 shadow" width="300" height="100">
                        </div>
                        <div class="col-md-6 order-md-1">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">1</div>
                                <h4 class="ms-3 mb-0">Create a free account</h4>
                            </div>
                            <p class="text-muted">Sign up for BookSync in seconds to access our extensive digital library and start exploring thousands of books.</p>
                        </div>
                    </div>
                    <div class="row align-items-center mb-5">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <img src="/api/placeholder/500/300" alt="Browse Books" class="img-fluid rounded-3 shadow">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">2</div>
                                <h4 class="ms-3 mb-0">Browse and preview</h4>
                            </div>
                            <p class="text-muted">Explore our categories to find books that interest you and enjoy a 3-day free preview period to sample the content.</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                            <img src="/api/placeholder/500/300" alt="Purchase Books" class="img-fluid rounded-3 shadow">
                        </div>
                        <div class="col-md-6 order-md-1">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">3</div>
                                <h4 class="ms-3 mb-0">Purchase and enjoy</h4>
                            </div>
                            <p class="text-muted">Buy books you love to read them indefinitely and download PDF versions for offline access on your desktop.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-5" id="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Simple, Fair Pricing</h2>
                <p class="text-muted">Access BookSync for free and only pay for the books you love</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="pricing-card highlighted">
                        <div class="highlight-badge">Most Popular</div>
                        <h3 class="fw-bold mb-4">Account Creation</h3>
                        <div class="display-5 fw-bold mb-3 text-primary">Free</div>
                        <p class="text-muted mb-4">Create your account and start exploring</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Unlimited browsing</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> 3-day book previews</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Reading history tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Wishlist management</li>
                        </ul>
                        <a href="/register" class="btn btn-primary w-100">Sign Up Free</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="pricing-card">
                        <h3 class="fw-bold mb-4">Book Purchases</h3>
                        <div class="display-5 fw-bold mb-3 text-primary">Varies</div>
                        <p class="text-muted mb-4">Pay only for the books you love</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> One-time purchase</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Unlimited reading access</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> PDF download available</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Desktop reading support</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Add to your personal library</li>
                        </ul>
                        <a href="/login" class="btn btn-outline-primary w-100">Browse Books</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-light" id="faq">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Frequently Asked Questions</h2>
                <p class="text-muted">Find answers to common questions about BookSync</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-item">
                        <button class="faq-question">How does the 3-day preview work?</button>
                        <div class="faq-answer">
                            <p>When you find a book you're interested in, you can start a 3-day preview for free. During this period, you can read the book online to determine if you want to purchase it. After the preview period expires, you'll need to buy the book to continue reading it.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">What does it mean that only 3 users can read a book at once?</button>
                        <div class="faq-answer">
                            <p>To ensure authors are fairly compensated and to maintain exclusive reading experiences, each book can only be accessed by 3 different users at the same time during the free preview period. If a book has reached its maximum reader limit, you'll need to wait until someone's preview period ends or purchase the book immediately.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">Can I read BookSync books on my mobile device?</button>
                        <div class="faq-answer">
                            <p>Currently, BookSync only supports desktop reading. We're working on mobile support which will be available in a future update. For now, you can access your purchased books and previews from any desktop browser.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">What happens when I purchase a book?</button>
                        <div class="faq-answer">
                            <p>When you purchase a book, you gain permanent access to read it online through BookSync. Additionally, you can download the book as a PDF file for offline reading on your desktop. Your purchases are stored in your personal library for easy access anytime.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">How do I add books to my wishlist?</button>
                        <div class="faq-answer">
                            <p>While browsing our catalog, you can click the heart icon on any book to add it to your wishlist. Your wishlist is accessible from your account dashboard, making it easy to keep track of books you're interested in purchasing in the future.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-4">Ready to Start Reading?</h2>
                    <p class="lead mb-4">Join thousands of readers who've discovered their next favorite book on BookSync. Create your free account today and start exploring.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/register" class="btn btn-light">Create Free Account</a>
                        <a href="/login" class="btn btn-outline-light">Browse Books</a>
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
                    <a href="/contact-us" class="text-decoration-none text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Get in touch with us">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all FAQ question buttons
            const faqQuestions = document.querySelectorAll('.faq-question');

            // Add click event listener to each question
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    // First close all open FAQ items
                    faqQuestions.forEach(item => {
                        // Skip the current item that was clicked
                        if (item !== this) {
                            item.classList.remove('active');
                            item.nextElementSibling.classList.remove('active');
                        }
                    });

                    // Toggle active class on the clicked question
                    this.classList.toggle('active');

                    // Get the answer element (next sibling after the button)
                    const answer = this.nextElementSibling;

                    // Toggle active class on the answer
                    answer.classList.toggle('active');
                });
            });
        });
    </script>
</body>

</html>