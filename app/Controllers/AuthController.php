<?php

namespace App\Controllers;

use DateTime;

use Core\AvatarGenerator;
use Core\Session;
use Core\Cookie;
use App\Models\ActivityLogModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $activityLogModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
        $this->activityLogModel = new ActivityLogModel();
        
        // Clean up expired tokens on controller initialization
        $this->userModel->cleanupExpiredTokens();
    }

    public function registerForm() 
    {
        $this->render('auth/register');
    }

    public function loginForm() 
    {
        $this->render('auth/login');
    }

    public function login() 
    {
        if (!$this->isPost() || !$this->isAjax()) {
            return $this->jsonError('Invalid request method');
        }

        $data = $this->getJsonInput();
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $remember = isset($data['remember']);

        // Get user record but check for soft deletion
        $user = $this->userModel->findByEmail($email);
        
        // Check if user exists
        if (!$user) {
            // Log failed login attempt
            $this->activityLogModel->logActivity(null, 'LOGIN', "Failed login attempt for email: $email (User not found)");
            return $this->jsonError('Invalid email or password');
        }
        
        // Check if account is soft deleted
        if ($user['ua_deleted_at'] !== null) {
            $this->activityLogModel->logActivity(null, 'LOGIN', "Failed login attempt for deactivated account: $email");
            return $this->jsonError('This account has been deactivated');
        }
        
        // Check password and active status
        if (!$this->userModel->verifyPassword($password, $user['ua_hashed_password'])) {
            $this->activityLogModel->logActivity($user['ua_id'], 'LOGIN', "Failed login attempt: incorrect password");
            return $this->jsonError('Invalid email or password');
        }
        
        if (!$user['ua_is_active']) {
            $this->activityLogModel->logActivity($user['ua_id'], 'LOGIN', "Failed login attempt: account inactive");
            return $this->jsonError('Account is inactive');
        }

        // Update last login timestamp
        $this->userModel->updateLastLogin($user['ua_id']);

        // Set session data
        Session::set('user_id', $user['ua_id']);
        Session::set('profile_url', $user['ua_profile_url']);
        Session::set('first_name', $user['ua_first_name']);
        Session::set('last_name', $user['ua_last_name']);
        Session::set('full_name', $user['ua_first_name'] . ' ' . $user['ua_last_name']);
        Session::set('user_email', $user['ua_email']);
        Session::set('phone_number', $user['ua_phone_number']);
        Session::set('user_role', $user['role_name'] ?? "user");
        Session::set('member_since', (new DateTime($user['ua_created_at']))->format('F j, Y'));

        if ($remember) {
            $token = $this->userModel->generateRememberToken($user['ua_id'], 30);
            Cookie::set('remember_token', $token, 30);
        }

        $role = $user['role_name'] ?? "/";

        $redirectUrl = match ($role) {
            'admin'     => '/admin/dashboard',
            'user'      => '/user/dashboard',
            default     => '/'
        };

        if ($role === "admin") {
            Session::set("profile_route", '/admin/admin-profile');
        } else {
            Session::set("profile_route", '/user/user-profile');
        }

        // Log successful login
        $this->activityLogModel->logActivity($user['ua_id'], 'LOGIN', "User logged in successfully");

        return $this->jsonSuccess(
            ['redirect_url' => $redirectUrl],
            'Login successful'
        );
    }

    public function register() 
    {
        $avatar = new AvatarGenerator();

        if (!$this->isPost() || !$this->isAjax()) {
            return $this->jsonError('Invalid request method');
        }
        $data = $this->getJsonInput();

        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field] ?? '')) {
                return $this->jsonError('All fields are required');
            }
        }

        $profileUrl = $avatar->generate($data['first_name'] . ' ' . $data['last_name']);
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $email = $data['email'];
        $password = $data['password'];
        $phoneNumber = $data['phone_number'] ?? null;

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('Invalid email format');
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $this->activityLogModel->logActivity(null, 'REGISTER', "Registration failed: email already exists ($email)");
            return $this->jsonError('Email already exists');
        }

        // Create the user
        $result = $this->userModel->createUser([
            'profile_url' => $profileUrl,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'phone_number' => $phoneNumber,
            'role_id' => 1, // Default role 'user'
            'is_active' => true
        ]);

        if ($result) {
            // Log successful registration
            $this->activityLogModel->logActivity($result, 'REGISTER', "New user registered: $firstName $lastName ($email)");
            
            return $this->jsonSuccess(
                ['redirect_url' => '/login'],
                'User registered successfully'
            );
        } else {
            $this->activityLogModel->logActivity(null, 'REGISTER', "Registration failed for $email");
            return $this->jsonError('Registration failed');
        }
    }

    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $userEmail = $_SESSION['user_email'] ?? 'Unknown user';
        
        // Clear "remember me" token from DB if set
        if ($userId) {
            $this->userModel->clearRememberToken($userId);
        }

        // Log logout activity before destroying session
        $this->activityLogModel->logActivity($userId, 'LOGOUT', "User logged out: $userEmail");

        // Remove session data
        Session::clear();
        Session::destroy();

        // Remove "remember me" cookie if it exists
        if (Cookie::has('remember_token')) {
            Cookie::delete('remember_token');
        }

        // Flash logout success message 
        Session::flash("success", "Logout successful");

        // Redirect to login page
        $this->redirect("/login");
    }

    /**
     * Check if user is already logged in via remember token
     * Called on application startup
     */
    public function checkRememberToken()
    {
        // If already logged in, skip this check
        if (isset($_SESSION['user_id'])) {
            return;
        }
        
        // Check for remember token cookie
        if (Cookie::has('remember_token')) {
            $token = Cookie::get('remember_token');
            $user = $this->userModel->findByRememberToken($token);
            
            // If valid token and user is active
            // findByRememberToken now includes expiration check
            if ($user && $user['ua_is_active']) {
                // Update last login timestamp
                $this->userModel->updateLastLogin($user['ua_id']);
                
                // Set session data
                $_SESSION['user_id'] = $user['ua_id'];
                $_SESSION['user_email'] = $user['ua_email'];
                $_SESSION['user_role'] = $user['role_name'];
                $_SESSION['full_name'] = $user['ua_first_name'] . ' ' . $user['ua_last_name'];
                $_SESSION['first_name'] = $user['ua_first_name'];
                $_SESSION['last_name'] = $user['ua_last_name'];
                $_SESSION['profile_url'] = $user['ua_profile_url'];
                $_SESSION['phone_number'] = $user['ua_phone_number'];
                
                // Generate a new token for security
                // This rotates the token on each successful auto-login
                $newToken = $this->userModel->generateRememberToken($user['ua_id'], 30);
                Cookie::set('remember_token', $newToken, 30);
                
                // Log automatic login via remember token
                $this->activityLogModel->logActivity($user['ua_id'], 'LOGIN', "Automatic login via remember token");
            } else {
                // Token is invalid or expired, clear cookie
                Cookie::delete('remember_token');
            }
        }
    }

    /**
     * Handle password reset request form
     */
    public function forgotPasswordForm()
    {
        $this->render('auth/forgot-password');
    }

    /**
     * Process password reset request
     */
    public function forgotPassword()
    {
        if (!$this->isPost() || !$this->isAjax()) {
            return $this->jsonError('Invalid request method');
        }

        $data = $this->getJsonInput();
        $email = $data['email'] ?? '';
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            // Don't reveal if email exists or not for security
            Session::flash("success", "If your email is registered, you will receive password reset instructions");
            return $this->jsonSuccess(null, 'Reset instructions sent if email exists');
        }
        
        // Here you would generate a reset token and send an email
        // This is just a placeholder - implement actual email sending logic
        $resetToken = bin2hex(random_bytes(32));
        
        // Store the token in the database (you'd need to add this field)
        // $this->userModel->updateUser($user['ua_id'], ['reset_token' => $resetToken]);
        
        // Send email with reset link
        // sendResetEmail($user['ua_email'], $resetToken);
        
        // Log password reset request
        $this->activityLogModel->logActivity($user['ua_id'], 'PROFILE_UPDATE', "Password reset requested");
        
        Session::flash("success", "Password reset instructions sent to your email");
        return $this->jsonSuccess(null, 'Reset instructions sent');
    }
}