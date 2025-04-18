<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Book Management System</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .register-container {
            max-width: 550px;
            width: 100%;
            padding: 1rem;
        }

        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 1.5rem 0.5rem;
            text-align: center;
        }

        .card-title {
            margin-bottom: 0;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .form-control {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(84, 105, 212, 0.1);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #e2e8f0;
            color: #718096;
        }

        .btn {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
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

        .btn-outline-secondary {
            color: #718096;
            border-color: #e2e8f0;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: var(--secondary-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #718096;
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .requirement i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
        }

        .valid-requirement {
            color: var(--accent-color);
        }

        .invalid-requirement {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="card shadow">
            <div class="card-header bg-white">
                <div class="logo text-center">
                    <i class="fas fa-book"></i> BookSync
                </div>
                <h4 class="card-title mb-4">Create an account</h4>
            </div>
            <div class="card-body p-4">
                <form id="registerForm">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="firstName" class="form-label">First name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="firstName" name="fname" placeholder="John" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="lastName" name="lname" placeholder="Doe" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                    </div>

                    <div class="mb-4">
                        <label for="confirmPassword" class="form-label">Confirm password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Terms of Service</a> and <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">Already have an account? <a href="/login" class="text-decoration-none" style="color: var(--primary-color);">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/utility/toast.js"></script>
    <?php include __DIR__ . '/../../Views/toast/toast-view.php' ?>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Validate password and confirm password
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                e.preventDefault(); // Stop form submission
                alert('Passwords do not match. Please re-enter.');
                document.getElementById('confirmPassword').focus();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="assets/js/utility/toast-notifications.js"></script>
    <script src="assets/js/utility/form-handler.js"></script>
    
    <script>
        handleFormSubmission('registerForm', '/register'); 
    </script>
</body>

</html>