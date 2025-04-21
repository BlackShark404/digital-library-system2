<?php

namespace App\Controllers;

use DateTime;

use Core\AvatarGenerator;
use Core\Session;
use Core\Cookie;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
        
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
            return $this->jsonError('Invalid email or password');
        }
        
        // Check if account is soft deleted
        if ($user['deleted_at'] !== null) {
            return $this->jsonError('This account has been deactivated');
        }
        
        // Check password and active status
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return $this->jsonError('Invalid email or password');
        }
        
        if (!$user['is_active']) {
            return $this->jsonError('Account is inactive');
        }

        // Update last login timestamp
        $this->userModel->updateLastLogin($user['id']);

        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['profile_url'] = $user['profile_url'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'] ?? "user";
        $_SESSION['member_since'] = (new DateTime($user['created_at']))->format('F j, Y');


        if ($remember) {
            $token = $this->userModel->generateRememberToken($user['id'], 30);
            Cookie::set('remember_token', $token, 30);
        }

        $role = $user['role_name'] ?? "/";
        $redirectUrl = match ($role) {
            'admin'     => '/admin/dashboard',
            'user'      => '/user/dashboard',
            default     => '/'
        };

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
        $last_name = $data['last_name'];
        $email = $data['email'];
        $password = $data['password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('Invalid email format');
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return $this->jsonError('Email already exists');
        }

        // Create the user
        $result = $this->userModel->createUser([
            'profile_url' => $profileUrl,
            'first_name' => $firstName,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'role_id' => '1',
            'is_active' => true
        ]);

        if ($result) {
            return $this->jsonSuccess(
                ['redirect_url' => '/login'],
                'User registered successfully'
            );
        } else {
            return $this->jsonError('Registration failed');
        }
    }

    public function logout()
    {
        // Clear "remember me" token from DB if set
        if (isset($_SESSION['user_id'])) {
            $this->userModel->clearRememberToken($_SESSION['user_id']);
        }

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
            if ($user && $user['is_active']) {
                // Update last login timestamp
                $this->userModel->updateLastLogin($user['id']);
                
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                
                // Generate a new token for security
                // This rotates the token on each successful auto-login
                $newToken = $this->userModel->generateRememberToken($user['id'], 30);
                Cookie::set('remember_token', $newToken, 30);
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
        // $this->userModel->updateUser($user['id'], ['reset_token' => $resetToken]);
        
        // Send email with reset link
        // sendResetEmail($user['email'], $resetToken);
        
        Session::flash("success", "Password reset instructions sent to your email");
        return $this->jsonSuccess(null, 'Reset instructions sent');
    }
}